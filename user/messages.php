<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all dealers who have messaged this user or whom this user messaged
$query = "
    SELECT DISTINCT u.id, u.full_name 
    FROM users u
    JOIN messages m ON ( (m.sender_id = u.id AND m.receiver_id = ?) OR (m.receiver_id = u.id AND m.sender_id = ?) )
    WHERE u.full_name = 'dealer'
    ORDER BY u.full_name
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$dealers = [];
while ($row = $result->fetch_assoc()) {
    $dealers[] = $row;
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['dealer_id'])) {
    $message = trim($_POST['message']);
    $dealer_id = intval($_POST['dealer_id']);
    if ($message && $dealer_id) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $dealer_id, $message);
        $stmt->execute();
        header("Location: messages.php?dealer_id=$dealer_id");
        exit();
    }
}

// Get selected dealer_id from query param or default to first dealer
$selected_dealer_id = isset($_GET['dealer_id']) ? intval($_GET['dealer_id']) : ($dealers[0]['id'] ?? null);

$messages = [];
if ($selected_dealer_id) {
    // Fetch messages between user and selected dealer
    $stmt = $conn->prepare("
        SELECT sender_id, receiver_id, message, sent_at 
        FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC
    ");
    $stmt->bind_param("iiii", $user_id, $selected_dealer_id, $selected_dealer_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Messages | SwapRide Kenya</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        .chat-container {
            display: flex;
            height: 70vh;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .chat-list {
            width: 25%;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            background: #f9f9f9;
        }
        .chat-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .chat-list li {
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
        }
        .chat-list li.active, .chat-list li:hover {
            background: #007bff;
            color: white;
        }
        .chat-window {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background: #fff;
        }
        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 20px;
            clear: both;
            position: relative;
        }
        .message.user {
            background: #007bff;
            color: white;
            float: right;
            text-align: right;
        }
        .message.dealer {
            background: #e9e9eb;
            color: #333;
            float: left;
            text-align: left;
        }
        .sent-at {
            font-size: 0.75em;
            color: #777;
            margin-top: 5px;
        }
        .chat-input {
            padding: 10px;
            border-top: 1px solid #ddd;
            background: #f4f4f4;
            display: flex;
        }
        .chat-input textarea {
            flex-grow: 1;
            resize: none;
            border-radius: 20px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .chat-input button {
            margin-left: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1em;
        }
        .chat-input button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="messages.php" class="active">Messages</a></li>
                <li><a href="finance.php">Finance Options</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container">
        <h2>Chat with Dealers</h2>
        <div class="chat-container">
            <div class="chat-list">
                <ul>
                    <?php foreach ($dealers as $dealer): ?>
                        <li class="<?= ($dealer['id'] == $selected_dealer_id) ? 'active' : '' ?>">
                            <a href="?dealer_id=<?= $dealer['id'] ?>" style="color:inherit; text-decoration:none;">
                                <?= htmlspecialchars($dealer['username']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if(empty($dealers)): ?>
                        <li><em>No conversations yet.</em></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="chat-window">
                <div class="messages" id="messages">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="message <?= ($msg['sender_id'] == $user_id) ? 'user' : 'dealer' ?>">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                <div class="sent-at"><?= date('d M Y, h:i A', strtotime($msg['sent_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><em>No messages in this conversation.</em></p>
                    <?php endif; ?>
                </div>

                <?php if ($selected_dealer_id): ?>
                <form method="POST" class="chat-input">
                    <textarea name="message" rows="2" placeholder="Type your message here..." required></textarea>
                    <input type="hidden" name="dealer_id" value="<?= $selected_dealer_id ?>">
                    <button type="submit">Send</button>
                </form>
                <?php else: ?>
                    <p><em>Select a dealer to start chatting.</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
