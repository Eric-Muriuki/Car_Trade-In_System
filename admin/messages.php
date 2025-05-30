<?php
session_start();
require_once '../includes/db_connect.php';

// Admin auth check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
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
    $tickets_res = $conn->query("SELECT t.id, t.subject, t.status, t.created_at, u.full_name AS user_name, d.business_name AS dealer_name 
                                 FROM support_tickets t
                                 LEFT JOIN users u ON t.user_id = u.id
                                 LEFT JOIN dealers d ON t.dealer_id = d.id
                                 ORDER BY t.created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Support Messages - Admin Panel</title>

<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
    /* Reset & base */
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FFFFFA;
        color: #00232A;
        line-height: 1.6;
    }
    a {
        color: #FE0000;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    a:hover {
        color: #AF0000;
    }
    /* Navbar */
    .navbar {
        background-color: #00232A;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        color: #FFFFFA;
        font-weight: 600;
    }
    .nav-title {
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nav-title i {
        color: #FE0000;
    }
    .nav-links {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .nav-links a {
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 500;
        background-color: transparent;
    }
    .nav-links a:hover {
        background-color: #FF9B9B;
        color: #730000;
    }
    .nav-links a.logout {
        background-color: #730000;
        color: #FFFFFA;
        font-weight: 700;
    }
    .nav-links a.logout:hover {
        background-color: #FE0000;
        color: #FFFFFA;
    }

    /* Container */
    .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #FFFFFA;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(115, 0, 0, 0.3);
    }
    h2, h3 {
        color: #730000;
        margin-bottom: 20px;
    }
    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 0 8px rgba(115,0,0,0.15);
        border-radius: 8px;
        overflow: hidden;
    }
    thead {
        background-color: #FE0000;
        color: #FFFFFA;
    }
    thead tr th {
        padding: 12px 15px;
        font-weight: 600;
        text-align: left;
    }
    tbody tr {
        border-bottom: 1px solid #AF0000;
        transition: background-color 0.25s ease;
    }
    tbody tr:hover {
        background-color: #FF9B9B;
        color: #730000;
        cursor: pointer;
    }
    tbody tr td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    tbody tr td a {
        font-weight: 600;
        color: #00232A;
    }
    tbody tr td a:hover {
        color: #FE0000;
        text-decoration: underline;
    }

    /* Message container */
    #message-container {
        border: 1px solid #AF0000;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
        background: #FF9B9B;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    #message-container > div {
        margin-bottom: 15px;
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 15px;
        word-wrap: break-word;
        font-size: 0.95rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    #message-container > div.admin {
        background: #D1E7DD;
        color: #00232A;
        margin-left: auto;
        text-align: right;
    }
    #message-container > div.user {
        background: #F8D7DA;
        color: #730000;
        margin-right: auto;
        text-align: left;
    }
    #message-container small {
        font-size: 0.75rem;
        color: #730000;
    }
    #message-container strong {
        font-weight: 600;
    }

    /* Reply form */
    #replyForm textarea {
        width: 100%;
        resize: vertical;
        padding: 12px;
        font-size: 1rem;
        border: 2px solid #AF0000;
        border-radius: 8px;
        background: #FFFFFA;
        color: #00232A;
        transition: border-color 0.3s ease;
        min-height: 100px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    #replyForm textarea:focus {
        border-color: #FE0000;
        outline: none;
    }
    #replyForm button {
        margin-top: 12px;
        padding: 12px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        background-color: #FE0000;
        color: #FFFFFA;
        cursor: pointer;
        transition: background-color 0.3s ease;
        box-shadow: 0 5px 10px rgba(254, 0, 0, 0.4);
        width: 100%;
        max-width: 200px;
    }
    #replyForm button:hover {
        background-color: #AF0000;
        box-shadow: 0 5px 12px rgba(175, 0, 0, 0.7);
    }

    /* Back link */
    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #00232A;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        color: #FE0000;
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            gap: 10px;
        }
        .nav-links {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        .nav-links a {
            text-align: center;
        }
        #message-container {
            max-height: 300px;
        }
    }
</style>
</head>
<body>

<nav class="navbar">
    <div class="nav-title"><i class="fa-solid fa-headset"></i> Support Admin Panel</div>
    <div class="nav-links">
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="support_tickets.php"><i class="fa-solid fa-ticket"></i> Tickets</a>
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</nav>

<div class="container">

<?php if (!$ticket_id): ?>
    <h2>All Support Tickets</h2>
    <?php if ($tickets_res->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Customer/Dealer</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($t = $tickets_res->fetch_assoc()): ?>
            <tr onclick="window.location.href='support_messages.php?ticket_id=<?= $t['id'] ?>'">
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['user_name'] ?: $t['dealer_name']) ?></td>
                <td><?= htmlspecialchars(ucfirst($t['status'])) ?></td>
                <td><?= date("M d, Y H:i", strtotime($t['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No tickets found.</p>
    <?php endif; ?>

<?php else: ?>
    <a href="support_messages.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Tickets</a>

    <h3>Ticket: <?= htmlspecialchars($ticket['subject']) ?></h3>
    <p><strong>From:</strong> <?= htmlspecialchars($ticket['user_name'] ?: $ticket['dealer_name']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($ticket['status'])) ?></p>
    <p><strong>Created:</strong> <?= date("M d, Y H:i", strtotime($ticket['created_at'])) ?></p>

    <div id="message-container" aria-live="polite" aria-atomic="true" role="log">
        <?php
        if ($messages->num_rows === 0) {
            echo "<p><em>No messages yet.</em></p>";
        } else {
            while ($msg = $messages->fetch_assoc()):
                $senderClass = $msg['sender_type'] === 'admin' ? 'admin' : 'user';
                $senderName = ($msg['sender_type'] === 'admin') ? 'Admin' : (($partner_type === 'user') ? $ticket['user_name'] : $ticket['dealer_name']);
                $time = date("M d, Y H:i", strtotime($msg['sent_at']));
                ?>
                <div class="<?= $senderClass ?>" tabindex="0">
                    <strong><?= htmlspecialchars($senderName) ?></strong><br />
                    <?= nl2br(htmlspecialchars($msg['message'])) ?><br />
                    <small><?= $time ?></small>
                </div>
            <?php endwhile; 
        }
        ?>
    </div>

    <form id="replyForm" method="post" action="" autocomplete="off" aria-label="Reply to support ticket">
        <textarea name="reply_message" id="reply_message" placeholder="Type your reply here..." required aria-required="true" rows="4" spellcheck="true"></textarea>
        <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>" />
        <button type="submit" aria-label="Send reply">Send Reply</button>
    </form>
<?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('replyForm');
    if (!form) return;

    const msgContainer = document.getElementById('message-container');
    const textarea = form.querySelector('textarea');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = textarea.value.trim();
        if (message === '') {
            alert('Message cannot be empty');
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            if (result.success) {
                // Append the new message on top
                const div = document.createElement('div');
                div.classList.add('admin');
                div.setAttribute('tabindex', '0');

                const now = new Date();
                const timeStr = now.toLocaleString('en-US', {
                    month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                div.innerHTML = `<strong>Admin</strong><br>${message.replace(/\n/g, '<br>')}<br><small>${timeStr}</small>`;

                msgContainer.appendChild(div);
                msgContainer.scrollTop = msgContainer.scrollHeight;

                textarea.value = '';
                textarea.focus();
            } else {
                alert(result.message || 'Failed to send reply.');
            }
        } catch (err) {
            alert('Error sending reply.');
            console.error(err);
        }
    });
});
</script>

</body>
</html>
