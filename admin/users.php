<?php
session_start();
require_once '../includes/db-connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle block/unblock actions
if (isset($_GET['block_id'])) {
    $block_id = intval($_GET['block_id']);
    $stmt = $conn->prepare("UPDATE users SET is_blocked = 1 WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit();
}

if (isset($_GET['unblock_id'])) {
    $unblock_id = intval($_GET['unblock_id']);
    $stmt = $conn->prepare("UPDATE users SET is_blocked = 0 WHERE id = ?");
    $stmt->bind_param("i", $unblock_id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit();
}

// Fetch all users and dealers
$sql = "SELECT id, fullname, email, user_type, is_blocked, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<?php include '../includes/header.php'; ?>

<div class="container" style="max-width: 960px; margin: 40px auto; padding: 20px;">
    <h2>Manage Users</h2>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #007bff; color: #fff;">
                <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Full Name</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                <th style="padding: 10px; border: 1px solid #ddd;">User Type</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Registered On</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr style="background-color: <?= $user['is_blocked'] ? '#f8d7da' : '#fff' ?>;">
                        <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($user['id']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($user['fullname']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-transform: capitalize;"><?= htmlspecialchars($user['user_type']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; color: <?= $user['is_blocked'] ? 'red' : 'green' ?>;">
                            <?= $user['is_blocked'] ? 'Blocked' : 'Active' ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php if ($user['is_blocked']): ?>
                                <a href="users.php?unblock_id=<?= $user['id'] ?>" style="color: green; text-decoration:none;">Unblock</a>
                            <?php else: ?>
                                <a href="users.php?block_id=<?= $user['id'] ?>" style="color: red; text-decoration:none;" onclick="return confirm('Are you sure you want to block this user?');">Block</a>
                            <?php endif; ?>
                            | 
                            <a href="edit_user.php?id=<?= $user['id'] ?>" style="text-decoration:none;">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="padding: 10px; text-align: center;">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
