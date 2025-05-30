<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch list of unique users who have messaged or been messaged by this dealer
$users_sql = "
    SELECT DISTINCT u.id, u.full_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.receiver_id = ?
    UNION
    SELECT DISTINCT u.id, u.full_name
    FROM messages m
    JOIN users u ON m.receiver_id = u.id
    WHERE m.sender_id = ?
    ORDER BY full_name
";
$stmt_users = $conn->prepare($users_sql);
$stmt_users->bind_param("ii", $dealer_id, $dealer_id);
$stmt_users->execute();
$users_result = $stmt_users->get_result();

// Handle chat view with selected user
$chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$messages = [];

if ($chat_user_id > 0) {
    $msg_sql = "
        SELECT * FROM messages 
        WHERE 
            (sender_id = ? AND receiver_id = ?)
         OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC
    ";
    $stmt_msg = $conn->prepare($msg_sql);
    $stmt_msg->bind_param("iiii", $dealer_id, $chat_user_id, $chat_user_id, $dealer_id);
    $stmt_msg->execute();
    $messages = $stmt_msg->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch the chat user's full name to display in header
    $user_stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $chat_user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $chat_user_name = $user_result->fetch_assoc()['full_name'] ?? 'Customer';
} else {
    $chat_user_name = '';
}

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $chat_user_id > 0) {
    $message = trim($_POST['message']);
    if ($message !== '') {
        $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iis", $dealer_id, $chat_user_id, $message);
        $stmt_insert->execute();
        header("Location: messages.php?user_id=" . $chat_user_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customer Messages</title>
  <style>
    /* Color palette blended & used throughout */
    :root {
      --color-red-bright: #FE0000;
      --color-red-dark: #AF0000;
      --color-cream: #FFFFFA;
      --color-pink-light: #FF9B9B;
      --color-dark-blue: #00232A;
      --color-maroon: #730000;

      --bg-light: var(--color-cream);
      --bg-chat-sent: #ffdede; /* blend pink light & cream */
      --bg-chat-received: #c9f0f5; /* light blueish for contrast */
      --border-color: var(--color-maroon);
      --text-color-dark: var(--color-dark-blue);
      --accent-color: var(--color-red-bright);
      --btn-approve-bg: var(--color-dark-blue);
      --btn-approve-hover: var(--color-red-bright);
      --btn-reject-bg: var(--color-maroon);
      --btn-reject-hover: var(--color-red-dark);
    }

    /* Reset */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg-light);
      color: var(--text-color-dark);
    }

    /* Container flex for layout */
    .container {
      display: flex;
      flex-wrap: wrap;
      margin: 20px auto;
      width: 95%;
      max-width: 1200px;
      height: 80vh;
      border: 2px solid var(--border-color);
      border-radius: 10px;
      background: white;
      box-shadow: 0 0 12px rgba(115, 0, 0, 0.4);
    }

    /* Sidebar user list */
    .user-list {
      flex: 1 1 280px;
      max-width: 300px;
      background: linear-gradient(135deg, var(--color-pink-light), var(--color-maroon));
      color: white;
      border-right: 3px solid var(--color-maroon);
      padding: 20px 15px;
      overflow-y: auto;
      transition: all 0.3s ease;
    }
    .user-list h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-weight: 700;
      border-bottom: 2px solid var(--color-red-bright);
      padding-bottom: 5px;
      text-align: center;
      letter-spacing: 1.2px;
    }
    .user-item {
      margin-bottom: 10px;
    }
    .user-item a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background-color 0.3s ease, color 0.3s ease;
      font-weight: 600;
    }
    .user-item a:hover {
      background-color: var(--color-red-bright);
      color: var(--color-cream);
    }
    .user-item a.active {
      background-color: var(--color-dark-blue);
      color: var(--color-cream);
      font-weight: 700;
      box-shadow: 0 0 6px var(--color-red-bright);
    }

    /* Chat box */
    .chat-box {
      flex: 3 1 600px;
      display: flex;
      flex-direction: column;
      padding: 20px;
      background: var(--bg-light);
      border-radius: 0 10px 10px 0;
      box-shadow: inset 0 0 10px #ffe6e6;
      overflow: hidden;
    }
    .chat-box h3 {
      margin-top: 0;
      margin-bottom: 20px;
      font-weight: 700;
      color: var(--color-maroon);
      border-bottom: 2px solid var(--color-red-dark);
      padding-bottom: 10px;
      text-align: center;
      letter-spacing: 1.1px;
    }

    /* Chat history */
    .chat-history {
      flex-grow: 1;
      overflow-y: auto;
      padding-right: 15px;
      border: 2px solid var(--color-maroon);
      background: var(--color-cream);
      border-radius: 8px;
      margin-bottom: 20px;
      scrollbar-width: thin;
      scrollbar-color: var(--color-maroon) var(--color-pink-light);
    }
    /* Scrollbar styling for Webkit browsers */
    .chat-history::-webkit-scrollbar {
      width: 8px;
    }
    .chat-history::-webkit-scrollbar-track {
      background: var(--color-pink-light);
      border-radius: 8px;
    }
    .chat-history::-webkit-scrollbar-thumb {
      background-color: var(--color-maroon);
      border-radius: 8px;
      border: 2px solid var(--color-pink-light);
    }

    /* Message bubbles */
    .message {
      margin: 8px 0;
      padding: 12px 18px;
      border-radius: 20px;
      max-width: 70%;
      clear: both;
      word-wrap: break-word;
      font-size: 1rem;
      line-height: 1.4;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      position: relative;
      display: inline-block;
    }
    .sent {
      background: var(--bg-chat-sent);
      float: right;
      text-align: right;
      border-top-right-radius: 4px;
      border-bottom-left-radius: 20px;
      color: var(--color-maroon);
    }
    .received {
      background: var(--bg-chat-received);
      float: left;
      text-align: left;
      border-top-left-radius: 4px;
      border-bottom-right-radius: 20px;
      color: var(--color-dark-blue);
    }

    /* Timestamp */
    .message small {
      display: block;
      margin-top: 6px;
      font-size: 0.75em;
      color: var(--color-maroon);
      font-weight: 600;
    }

    /* Action buttons container */
    .message-actions {
      margin-top: 6px;
      display: flex;
      justify-content: flex-end;
      gap: 8px;
    }

    /* Buttons for Approve / Reject */
    .btn-action {
      padding: 4px 12px;
      border: none;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      color: var(--color-cream);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .btn-approve {
      background-color: var(--btn-approve-bg);
    }
    .btn-approve:hover,
    .btn-approve:focus {
      background-color: var(--btn-approve-hover);
      outline: none;
    }
    .btn-reject {
      background-color: var(--btn-reject-bg);
    }
    .btn-reject:hover,
    .btn-reject:focus {
      background-color: var(--btn-reject-hover);
      outline: none;
    }

    /* Message form */
    .message-form {
      margin-top: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .message-form textarea {
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      border: 2px solid var(--color-maroon);
      border-radius: 10px;
      resize: vertical;
      min-height: 80px;
      font-family: inherit;
      color: var(--color-dark-blue);
      transition: border-color 0.3s ease;
    }
    .message-form textarea:focus {
      border-color: var(--color-red-bright);
      outline: none;
      background: #fff5f5;
    }
    .message-form button {
      align-self: flex-end;
      padding: 10px 24px;
      background: var(--color-maroon);
      color: var(--color-cream);
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .message-form button:hover,
    .message-form button:focus {
      background: var(--color-red-bright);
      outline: none;
    }

    /* Responsive tweaks */
    @media (max-width: 900px) {
      .container {
        height: auto;
        flex-direction: column;
        border-radius: 10px;
      }
      .user-list {
        max-width: 100%;
        width: 100%;
        height: 150px;
        border-right: none;
        border-bottom: 3px solid var(--color-maroon);
        display: flex;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 10px 5px;
      }
      .user-item {
        margin: 0 10px 0 0;
        flex-shrink: 0;
      }
      .user-item a {
        padding: 10px 15px;
        white-space: nowrap;
        font-size: 0.9rem;
      }
      .chat-box {
        width: 100%;
        height: 400px;
        border-radius: 0 0 10px 10px;
        padding: 15px;
      }
      .chat-history {
        height: 300px;
        margin-bottom: 15px;
      }
    }

    @media (max-width: 500px) {
      .message-form textarea {
        font-size: 0.9rem;
      }
      .message-form button {
        padding: 8px 16px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
<div class="container" id="container">
  <div class="user-list" id="userList">
    <h3>Customers</h3>
    <?php while ($user = $users_result->fetch_assoc()): ?>
      <div class="user-item">
        <a href="?user_id=<?= $user['id'] ?>" <?= ($user['id'] == $chat_user_id) ? 'class="active"' : '' ?>>
          <?= htmlspecialchars($user['full_name']) ?>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

  <div class="chat-box">
    <?php if ($chat_user_id > 0): ?>
      <h3>Chat with <?= htmlspecialchars($chat_user_name) ?></h3>

      <div class="chat-history" id="chatHistory" tabindex="0" aria-live="polite">
        <?php if (count($messages) === 0): ?>
          <p>No messages yet. Start the conversation!</p>
        <?php endif; ?>
        <?php foreach ($messages as $msg): ?>
          <div class="message <?= ($msg['sender_id'] == $dealer_id) ? 'sent' : 'received' ?>">
            <?= nl2br(htmlspecialchars($msg['message'])) ?>
            <small><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></small>

            <?php if ($msg['sender_id'] == $dealer_id): // Show buttons only for dealer's messages ?>
              <div class="message-actions">
                <button class="btn-action btn-approve" onclick="handleAction(<?= $msg['id'] ?>, 'approve')">Approve</button>
                <button class="btn-action btn-reject" onclick="handleAction(<?= $msg['id'] ?>, 'reject')">Reject</button>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <form class="message-form" method="post" onsubmit="return validateMessage()">
        <textarea name="message" id="messageInput" placeholder="Type your message here..." required></textarea>
        <button type="submit">Send</button>
      </form>
    <?php else: ?>
      <p>Select a customer from the list to start chatting.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  // Auto-scroll chat history to bottom on page load
  const chatHistory = document.getElementById('chatHistory');
  if(chatHistory) {
    chatHistory.scrollTop = chatHistory.scrollHeight;
  }

  // Simple form validation before submit
  function validateMessage() {
    const input = document.getElementById('messageInput');
    if (!input.value.trim()) {
      alert('Please enter a message before sending.');
      return false;
    }
    return true;
  }

  // Handle Approve / Reject button clicks
  function handleAction(messageId, action) {
    const confirmMsg = action === 'approve' ? 
      'Are you sure you want to approve this offer?' : 
      'Are you sure you want to reject this offer?';
    
    if (!confirm(confirmMsg)) return;

    // For demo: simulate an action, you can implement ajax or form submission here
    alert(`Message ID ${messageId} has been ${action}d.`);

    // Optionally, here you could submit a form or send AJAX to backend to update status
    // location.href = `action_handler.php?message_id=${messageId}&action=${action}`;
  }
</script>
</body>
</html>
