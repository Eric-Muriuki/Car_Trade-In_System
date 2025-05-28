<?php
session_start();
require_once '../includes/db-connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle approve/reject actions for pending dealers
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND user_type = 'dealer'");
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dealers.php");
    exit();
}

if (isset($_GET['reject_id'])) {
    $reject_id = intval($_GET['reject_id']);
    $stmt = $conn->prepare("UPDATE users SET is_approved = -1 WHERE id = ? AND user_type = 'dealer'");
    $stmt->bind_param("i", $reject_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dealers.php");
    exit();
}

// Fetch pending dealers (is_approved = 0)
$sql_pending = "SELECT id, fullname, email, phone, kra_pin, created_at FROM users WHERE user_type = 'dealer' AND is_approved = 0 ORDER BY created_at DESC";
$result_pending = $conn->query($sql_pending);

// Fetch all approved or rejected dealers (is_approved = 1 or -1)
$sql_all = "SELECT id, fullname, email, phone, kra_pin, is_approved, created_at FROM users WHERE user_type = 'dealer' AND is_approved IN (1, -1) ORDER BY created_at DESC";
$result_all = $conn->query($sql_all);

?>

<?php include '../includes/header.php'; ?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 20px;">
    <h2>Manage Dealers</h2>

    <section style="margin-bottom: 40px;">
        <h3>Pending Dealer Accounts</h3>
        <?php if ($result_pending && $result_pending->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #ffc107; color: #000;">
                        <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Full Name</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Phone</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">KRA PIN</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Registered On</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dealer = $result_pending->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['id']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['fullname']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['email']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['phone']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['kra_pin']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y', strtotime($dealer['created_at'])) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <a href="dealers.php?approve_id=<?= $dealer['id'] ?>" style="color: green; text-decoration:none;" onclick="return confirm('Approve this dealer?');">Approve</a> | 
                                <a href="dealers.php?reject_id=<?= $dealer['id'] ?>" style="color: red; text-decoration:none;" onclick="return confirm('Reject this dealer?');">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending dealer accounts found.</p>
        <?php endif; ?>
    </section>

    <section>
        <h3>Approved / Rejected Dealers</h3>
        <?php if ($result_all && $result_all->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #17a2b8; color: #fff;">
                        <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Full Name</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Phone</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">KRA PIN</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dealer = $result_all->fetch_assoc()): ?>
                        <tr style="background-color: <?= $dealer['is_approved'] == 1 ? '#d4edda' : '#f8d7da' ?>;">
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['id']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['fullname']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['email']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['phone']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($dealer['kra_pin']) ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd; color: <?= $dealer['is_approved'] == 1 ? 'green' : 'red' ?>;">
                                <?= $dealer['is_approved'] == 1 ? 'Approved' : 'Rejected' ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y', strtotime($dealer['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No approved or rejected dealers found.</p>
        <?php endif; ?>
    </section>
</div>

<?php include '../includes/footer.php'; ?>
