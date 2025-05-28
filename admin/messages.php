<?php
session_start();
require_once '../includes/db_connect.php';

// Admin auth check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$ticket_id = $_GET['ticket_id'] ?? null;

// Handle reply POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'], $_POST['ticket_id'])) {
    $ticket_id_post = intval($_POST['ticket_id']);
    $reply = trim($_POST['reply_message']);
    if ($reply !== '') {
        // Fetch the ticket to determine receiver
        $stmt = $conn->prepare("SELECT user_id, dealer_id FROM support_tickets WHERE id = ?");
        $stmt->bind_param("i", $ticket_id_post);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit();
        }
        $ticket = $res->fetch_assoc();

        // Determine receiver type and ID
        $receiver_type = $ticket['user_id'] ? 'user' : 'dealer';
        $receiver_id = $ticket['user_id'] ?? $ticket['dealer_id'];

        // Insert reply into messages table
        $stmt = $conn->prepare("INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, trade_id, message) VALUES ('admin', ?, ?, ?, NULL, ?)");
        $admin_id = $_SESSION['admin_id'];
        $stmt->bind_param("isiss", $admin_id, $receiver_type, $receiver_id, $reply);
        $stmt->execute();

        // Update ticket status
        $conn->query("UPDATE support_tickets SET status='Pending' WHERE id=$ticket_id_post");

        echo json_encode(['success' => true, 'message' => 'Reply sent']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit();
    }
}

// Fetch tickets or messages
if ($ticket_id) {
    // Get ticket details
    $stmt = $conn->prepare("SELECT t.*, u.fullname AS user_name, d.business_name AS dealer_name 
                            FROM support_tickets t
                            LEFT JOIN users u ON t.user_id = u.id
                            LEFT JOIN dealers d ON t.dealer_id = d.id
                            WHERE t.id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $ticket_res = $stmt->get_result();
    if ($ticket_res->num_rows === 0) {
        die("Ticket not found");
    }
    $ticket = $ticket_res->fetch_assoc();

    // Determine sender and receiver ID
    $partner_type = $ticket['user_id'] ? 'user' : 'dealer';
    $partner_id = $ticket['user_id'] ?? $ticket['dealer_id'];

    // Fetch messages from messages table where trade_id is NULL (support)
    $msg_stmt = $conn->prepare("SELECT * FROM messages 
                                WHERE trade_id IS NULL AND 
                                    ((sender_type='admin' AND sender_id=?) AND (receiver_type=? AND receiver_id=?)
                                    OR (sender_type=? AND sender_id=? AND receiver_type='admin'))
                                ORDER BY sent_at ASC");
    $admin_id = $_SESSION['admin_id'];
    $msg_stmt->bind_param("isisi", $admin_id, $partner_type, $partner_id, $partner_type, $partner_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();

} else {
    // List all tickets
    $tickets_res = $conn->query("SELECT t.id, t.subject, t.status, t.created_at, u.fullname AS user_name, d.business_name AS dealer_name 
                                 FROM support_tickets t
                                 LEFT JOIN users u ON t.user_id = u.id
                                 LEFT JOIN dealers d ON t.dealer_id = d.id
                                 ORDER BY t.created_at DESC");
}
?>


 <style>
        /* Basic responsive navbar styling */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #212529;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }
        .navbar a:hover {
            color: #ffc107;
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
        }
        .nav-title {
            font-size: 1.4rem;
            font-weight: bold;
        }
        .container {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-title">
        <i class="fas fa-shield-alt"></i> Admin Panel
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="dealers.php">Dealers</a>
        <a href="cars.php">Listings</a>
        <a href="trades.php">Trade Logs</a>
        <a href="reports.php">Reports</a>
        <a href="messages.php">Support</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container" style="max-width:900px; margin:40px auto; padding:20px;">
    <h2>Support Messages - Admin Panel</h2>

    <?php if (!$ticket_id): ?>
        <h3>Support Tickets</h3>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead style="background:#f5f5f5;">
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>From</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tickets_res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['user_name'] ?: $row['dealer_name'] ?: 'Unknown') ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><a href="?ticket_id=<?= $row['id'] ?>">View & Reply</a></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($tickets_res->num_rows === 0): ?>
                    <tr><td colspan="6" style="text-align:center;">No support tickets found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <a href="messages.php" style="text-decoration:none; margin-bottom:15px; display:inline-block;">&larr; Back to Tickets</a>

        <h3>Ticket #<?= $ticket['id'] ?>: <?= htmlspecialchars($ticket['subject']) ?></h3>
        <p><strong>From:</strong> <?= htmlspecialchars($ticket['user_name'] ?: $ticket['dealer_name'] ?: 'Unknown') ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($ticket['created_at']) ?></p>

        <div id="message-container" style="border:1px solid #ccc; padding:15px; max-height:400px; overflow-y:auto; background:#fafafa; margin-bottom:20px;">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div style="margin-bottom:15px; <?= $msg['sender_type'] === 'admin' ? 'text-align:right;' : 'text-align:left;' ?>">
                    <strong><?= ucfirst($msg['sender_type']) ?></strong> <small>(<?= $msg['created_at'] ?>)</small><br>
                    <div style="display:inline-block; background: <?= $msg['sender_type'] === 'admin' ? '#d1e7dd' : '#f8d7da' ?>; padding:10px; border-radius:10px; max-width:70%;">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <form id="replyForm">
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <textarea name="reply_message" id="reply_message" rows="4" placeholder="Type your reply here..." required style="width:100%; padding:10px;"></textarea>
            <button type="submit" style="margin-top:10px; padding:10px 20px;">Send Reply</button>
        </form>

        <script>
        document.getElementById('replyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            fetch('messages.php?ticket_id=<?= $ticket['id'] ?>', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                if(data.success) {
                    // Reload page to show new message
                    location.reload();
                } else {
                    alert(data.message || 'Error sending reply');
                }
              }).catch(() => alert('Request failed'));
        });
        </script>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
