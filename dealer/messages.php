<?php
session_start();
require_once '../includes/db-connect.php';

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
    JOIN users u ON (m.sender_id = u.id AND m.sender_role = 'user') 
                  OR (m.receiver_id = u.id AND m.receiver_role = 'user')
    WHERE (m.sender_id = ? AND m.sender_role = 'dealer') 
       OR (m.receiver_id = ? AND m.receiver_role = 'dealer')
    ORDER BY u.full_name
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
            (sender_id = ? AND sender_role = 'dealer' AND receiver_id = ? AND receiver_role = 'user')
         OR (sender_id = ? AND sender_role = 'user' AND receiver_id = ? AND receiver_role = 'dealer')
        ORDER BY created_at ASC
    ";
    $stmt_msg = $conn->prepare($msg_sql);
    $stmt_msg->bind_param("iiii", $dealer_id, $chat_user_id, $chat_user_id, $dealer_id);
    $stmt_msg->execute();
    $messages = $stmt_msg->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $chat_user_id > 0) {
    $message = trim($_POST['message']);
    if ($message !== '') {
        $insert_sql = "INSERT INTO messages (sender_id, receiver_id, sender_role, receiver_role, message) VALUES (?, ?, 'dealer', 'user', ?)";
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
  <meta charset="UTF-8">
  <title>Customer Messages</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      display: flex;
      margin: 20px auto;
      width: 95%;
      max-width: 1200px;
    }

    .user-list {
      width: 25%;
      background: #f7f7f7;
      border-right: 1px solid #ccc;
      padding: 10px;
    }

    .user-list h3 {
      margin-bottom: 10px;
    }

    .user-item {
      margin-bottom: 8px;
    }

    .user-item a {
      text-decoration: none;
      color: #333;
    }

    .chat-box {
      width: 75%;
      padding: 20px;
      background: #fff;
    }

    .message {
      margin: 10px 0;
      padding: 10px;
      border-radius: 6px;
      max-width: 60%;
      clear: both;
    }

    .sent {
      background: #d1e7dd;
      float: right;
      text-align: right;
    }

    .received {
      background: #f8d7da;
      float: left;
      text-align: left;
    }

    .message-form {
      margin-top: 20px;
    }

    .message-form textarea {
      width: 100%;
      padding: 10px;
      height: 80px;
    }

    .message-form button {
      margin-top: 10px;
      padding: 8px 16px;
      background: #333;
      color: #fff;
      border: none;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="user-list">
    <h3>Customers</h3>
    <?php while ($user = $users_result->fetch_assoc()): ?>
      <div class="user-item">
        <a href="?user_id=<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></a>
      </div>
    <?php endwhile; ?>
  </div>

  <div class="chat-box">
    <?php if ($chat_user_id > 0): ?>
      <h3>Chat with <?= htmlspecialchars($_GET['user_name'] ?? 'Customer') ?></h3>

      <div class="chat-history">
        <?php foreach ($messages as $msg): ?>
          <div class="message <?= $msg['sender_role'] === 'dealer' ? 'sent' : 'received' ?>">
            <?= nl2br(htmlspecialchars($msg['message'])) ?><br>
            <small><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></small>
          </div>
        <?php endforeach; ?>
      </div>

      <form class="message-form" method="post">
        <textarea name="message" placeholder="Type your message here..." required></textarea>
        <button type="submit">Send</button>
      </form>
    <?php else: ?>
      <p>Select a customer to view the conversation.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
