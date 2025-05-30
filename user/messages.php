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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --red-primary: #FE0000;
            --red-dark: #AF0000;
            --red-soft: #FF9B9B;
            --red-deep: #730000;
            --whiteish: #FFFFFA;
            --blue-dark: #00232A;
            --shadow-light: rgba(254, 0, 0, 0.15);
            --shadow-medium: rgba(254, 0, 0, 0.3);
            --shadow-dark: rgba(175, 0, 0, 0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--blue-dark), var(--red-deep));
            color: var(--whiteish);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 960px;
            margin: auto;
            padding: 20px 15px;
            width: 100%;
        }

        /* Header */
        header.header {
            background: var(--whiteish);
            color: var(--blue-dark);
            box-shadow: 0 4px 15px var(--shadow-light);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--red-deep);
            text-shadow: 1px 1px 2px var(--red-soft);
            user-select: none;
        }

        nav ul.nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        nav ul.nav-links li {
            display: inline;
        }

        nav ul.nav-links li a {
            text-decoration: none;
            font-weight: 600;
            color: var(--red-dark);
            padding: 8px 15px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav ul.nav-links li a:hover,
        nav ul.nav-links li a.active {
            background: var(--red-primary);
            color: var(--whiteish);
            box-shadow: 0 4px 12px var(--shadow-medium);
        }

        nav ul.nav-links li a.btn {
            background: var(--red-primary);
            color: var(--whiteish);
            border: none;
            cursor: pointer;
        }

        /* Section */
        section.section {
            background: var(--whiteish);
            border-radius: 14px;
            padding: 30px 30px 25px;
            margin: 30px auto 40px;
            box-shadow:
                0 8px 24px var(--shadow-medium),
                inset 0 0 30px var(--red-soft);
            color: var(--blue-dark);
            display: flex;
            flex-direction: column;
        }

        section.section h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 25px;
            color: var(--red-deep);
            text-shadow: 1px 1px 3px var(--red-soft);
            text-align: center;
        }

        /* Chat Container */
        .chat-container {
            display: flex;
            height: 70vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px var(--shadow-light);
            background: var(--whiteish);
            border: 1px solid var(--red-soft);
        }

        .chat-list {
            width: 30%;
            background: var(--red-soft);
            overflow-y: auto;
            border-right: 2px solid var(--red-primary);
            transition: background 0.3s ease;
        }

        .chat-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .chat-list li {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid var(--red-deep);
            color: var(--red-deep);
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        .chat-list li:hover {
            background: var(--red-primary);
            color: var(--whiteish);
        }

        .chat-list li.active {
            background: var(--red-deep);
            color: var(--whiteish);
            box-shadow: inset 3px 0 6px var(--red-primary);
        }

        .chat-list li a {
            color: inherit;
            text-decoration: none;
            display: block;
        }

        /* Chat Window */
        .chat-window {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: var(--whiteish);
            border-radius: 0 12px 12px 0;
            box-shadow: inset 0 0 20px var(--red-soft);
        }

        .messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: var(--whiteish);
        }

        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 25px;
            clear: both;
            position: relative;
            font-size: 1rem;
            line-height: 1.4;
            box-shadow: 0 2px 8px var(--shadow-light);
            word-wrap: break-word;
        }

        .message.user {
            background: var(--red-primary);
            color: var(--whiteish);
            float: right;
            text-align: right;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px var(--shadow-dark);
        }

        .message.dealer {
            background: var(--red-soft);
            color: var(--red-deep);
            float: left;
            text-align: left;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 12px var(--shadow-medium);
        }

        .sent-at {
            font-size: 0.75em;
            color: var(--red-dark);
            margin-top: 6px;
            font-style: italic;
            user-select: none;
        }

        /* Chat Input */
        .chat-input {
            padding: 12px 15px;
            border-top: 2px solid var(--red-primary);
            background: var(--red-soft);
            display: flex;
            gap: 12px;
            border-radius: 0 0 12px 12px;
        }

        .chat-input textarea {
            flex-grow: 1;
            resize: none;
            border-radius: 20px;
            padding: 12px 18px;
            border: 1.5px solid var(--red-dark);
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
            min-height: 50px;
        }

        .chat-input textarea:focus {
            outline: none;
            border-color: var(--red-primary);
            box-shadow: 0 0 8px var(--red-primary);
            background: var(--whiteish);
            color: var(--red-deep);
        }

        .chat-input button {
            background: var(--red-primary);
            color: var(--whiteish);
            border: none;
            padding: 0 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            box-shadow: 0 4px 12px var(--shadow-dark);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
        }

        .chat-input button:hover,
        .chat-input button:focus {
            background: var(--red-deep);
            box-shadow: 0 6px 20px var(--shadow-dark);
            outline: none;
        }

        /* Footer */
        footer.footer {
            background: var(--red-deep);
            color: var(--whiteish);
            text-align: center;
            padding: 15px 10px;
            font-size: 0.9rem;
            box-shadow: 0 -4px 12px var(--shadow-dark);
            margin-top: auto;
            user-select: none;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .chat-container {
                flex-direction: column;
                height: auto;
                max-height: 80vh;
            }
            .chat-list {
                width: 100%;
                height: 150px;
                border-right: none;
                border-bottom: 2px solid var(--red-primary);
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                display: flex;
                gap: 8px;
            }
            .chat-list ul {
                display: flex;
                gap: 8px;
                padding: 0 10px;
            }
            .chat-list li {
                flex: 0 0 auto;
                border-bottom: none;
                border-radius: 20px;
                padding: 10px 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                white-space: nowrap;
            }
            .chat-window {
                height: 60vh;
                border-radius: 0 0 12px 12px;
            }
            .messages {
                padding: 15px 12px;
            }
            .message {
                max-width: 90%;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .chat-input textarea {
                font-size: 0.9rem;
                min-height: 40px;
            }
            .chat-input button {
                padding: 0 18px;
                font-size: 0.9rem;
            }
            nav ul.nav-links {
                gap: 12px;
            }
            nav ul.nav-links li a {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
            .logo {
                font-size: 1.5rem;
            }
            section.section h2 {
                font-size: 1.5rem;
            }
        }

        /* Scrollbar styling for chat lists and messages */
        .chat-list::-webkit-scrollbar,
        .messages::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }
        .chat-list::-webkit-scrollbar-track,
        .messages::-webkit-scrollbar-track {
            background: var(--red-soft);
            border-radius: 8px;
        }
        .chat-list::-webkit-scrollbar-thumb,
        .messages::-webkit-scrollbar-thumb {
            background: var(--red-primary);
            border-radius: 8px;
        }
        .chat-list::-webkit-scrollbar-thumb:hover,
        .messages::-webkit-scrollbar-thumb:hover {
            background: var(--red-deep);
        }
    </style>
</head>
<body>

<header class="header" role="banner">
    <div class="container">
        <h1 class="logo" aria-label="SwapRide Kenya">SwapRide Kenya</h1>
        <nav role="navigation" aria-label="Primary navigation">
            <ul class="nav-links">
                <li><a href="dashboard.php" aria-current="false">Dashboard</a></li>
                <li><a href="messages.php" class="active" aria-current="page">Messages</a></li>
                <li><a href="finance.php" aria-current="false">Finance Options</a></li>
                <li><a href="logout.php" class="btn" aria-current="false">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section" role="main" aria-label="Chat with Dealers">
    <div class="container">
        <h2>Chat with Dealers</h2>
        <div class="chat-container">
            <aside class="chat-list" role="region" aria-label="Dealer list">
                <ul>
                    <?php foreach ($dealers as $dealer): ?>
                        <li class="<?= ($dealer['id'] == $selected_dealer_id) ? 'active' : '' ?>" tabindex="0">
                            <a href="?dealer_id=<?= $dealer['id'] ?>" style="color:inherit; text-decoration:none;" aria-selected="<?= ($dealer['id'] == $selected_dealer_id) ? 'true' : 'false' ?>">
                                <?= htmlspecialchars($dealer['username']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if(empty($dealers)): ?>
                        <li><em>No conversations yet.</em></li>
                    <?php endif; ?>
                </ul>
            </aside>

            <section class="chat-window" role="region" aria-label="Chat messages">
                <div class="messages" id="messages" tabindex="0" aria-live="polite" aria-relevant="additions">
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
                <form method="POST" class="chat-input" aria-label="Send message form">
                    <textarea name="message" rows="2" placeholder="Type your message here..." required aria-required="true"></textarea>
                    <input type="hidden" name="dealer_id" value="<?= $selected_dealer_id ?>">
                    <button type="submit" aria-label="Send message">Send</button>
                </form>
                <?php else: ?>
                    <p><em>Select a dealer to start chatting.</em></p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>

<footer class="footer" role="contentinfo">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

<script>
    // Auto-scroll messages container to bottom on page load and on new messages
    const messagesContainer = document.getElementById('messages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Accessibility: Keyboard support for dealer list selection (Enter/Space)
    document.querySelectorAll('.chat-list li').forEach(item => {
        item.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = item.querySelector('a');
                if (link) link.click();
            }
        });
    });
</script>

</body>
</html>
