<?php
session_start();
require_once '../includes/db_connect.php';

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

// Fetch all users
$sql = "SELECT id, full_name, email, phone, user_type, is_blocked, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel - Manage Users</title>
<style>
  /* Reset & base */
  * {
    box-sizing: border-box;
  }
  body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #FFFFFA;
        color: #00232A;
    }

    .navbar {
        background-color: #730000;
        padding: 14px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }

    .navbar a {
        color: #FF9B9B;
        text-decoration: none;
        margin: 0 12px;
        font-weight: 500;
    }

    .navbar a:hover {
        color: #FE0000;
    }

    .nav-title {
        font-size: 1.4rem;
        font-weight: bold;
    }

  /* Container */
  .container {
    flex: 1;
    max-width: 960px;
    margin: 40px auto;
    background-color: #FFFFFA;
    padding: 30px 25px;
    box-shadow: 0 6px 18px rgba(0, 35, 42, 0.15);
    border-radius: 12px;
  }
  h2 {
    margin-bottom: 20px;
    color: #730000;
    font-weight: 700;
    text-align: center;
    letter-spacing: 0.5px;
  }

  /* Table styling */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgb(117 0 0 / 0.15);
  }
  thead tr {
    background: linear-gradient(90deg, #FE0000 0%, #AF0000 100%);
    color: #FFFFFA;
  }
  th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #FF9B9B;
    text-align: left;
  }
  tbody tr:nth-child(even) {
    background-color: #FF9B9B33; /* subtle translucent red */
  }
  tbody tr:hover {
    background-color: #FE000033;
  }
  tbody tr.blocked {
    background-color: #FF9B9B99 !important;
    color: #730000;
  }

  /* Status colors */
  .status-active {
    color: #00232A;
    font-weight: 700;
  }
  .status-blocked {
    color: #FE0000;
    font-weight: 700;
  }

  /* Actions links/buttons */
  .action-link {
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 6px;
    transition: background-color 0.3s ease;
    user-select: none;
  }
  .action-block {
    background-color: #FE0000;
    color: #FFFFFA;
    border: 1px solid #AF0000;
  }
  .action-block:hover {
    background-color: #AF0000;
  }
  .action-unblock {
    background-color: #00232A;
    color: #FF9B9B;
    border: 1px solid #00232A;
  }
  .action-unblock:hover {
    background-color: #730000;
    color: #FFFFFA;
    border-color: #730000;
  }
  .action-edit {
    background-color: transparent;
    color: #00232A;
    border: 1px solid #00232A;
  }
  .action-edit:hover {
    background-color: #00232A;
    color: #FFFFFA;
  }

  /* Responsive table wrapper */
  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Responsive adjustments */
  @media (max-width: 720px) {
    .nav-links {
      flex-direction: column;
      width: 100%;
      margin-top: 10px;
    }
    .nav-links a {
      padding: 10px;
      font-size: 1.1rem;
    }
    .container {
      margin: 20px 12px;
      padding: 20px 16px;
    }
    th, td {
      padding: 10px 8px;
      font-size: 0.9rem;
    }
  }
</style>

<script>
  // Confirm block/unblock with custom prompt
  function confirmAction(e, action) {
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
      e.preventDefault();
      return false;
    }
    return true;
  }
</script>

</head>
<body>

<div class="navbar" role="navigation" aria-label="Main Navigation">
    <div class="nav-title" tabindex="0" aria-label="Admin Panel Title">
        <i class="fas fa-shield-alt" aria-hidden="true"></i> Admin Panel
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php" aria-current="page" style="font-weight: 700; text-decoration: underline;">Users</a>
        <a href="dealers.php">Dealers</a>
        <a href="cars.php">Listings</a>
        <a href="trades.php">Trade Logs</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout</a>
    </div>
</div>

<div class="container" role="main">
    <h2>Manage Users</h2>
    <div class="table-wrapper" tabindex="0" aria-label="Users Data Table">
    <table>
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Full Name</th>
                <th scope="col">Email</th>
                <th scope="col">User Type</th>
                <th scope="col">Status</th>
                <th scope="col">Registered On</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr class="<?= $user['is_blocked'] ? 'blocked' : '' ?>">
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td style="text-transform: capitalize;"><?= htmlspecialchars($user['user_type']) ?></td>
                        <td class="<?= $user['is_blocked'] ? 'status-blocked' : 'status-active' ?>">
                            <?= $user['is_blocked'] ? 'Blocked' : 'Active' ?>
                        </td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['is_blocked']): ?>
                                <a href="users.php?unblock_id=<?= $user['id'] ?>" 
                                   class="action-link action-unblock" 
                                   onclick="return confirmAction(event, 'unblock');" 
                                   aria-label="Unblock user <?= htmlspecialchars($user['full_name']) ?>">
                                   Unblock
                                </a>
                            <?php else: ?>
                                <a href="users.php?block_id=<?= $user['id'] ?>" 
                                   class="action-link action-block" 
                                   onclick="return confirmAction(event, 'block');" 
                                   aria-label="Block user <?= htmlspecialchars($user['full_name']) ?>">
                                   Block
                                </a>
                            <?php endif; ?>
                            |
                            <a href="edit_user.php?id=<?= $user['id'] ?>" 
                               class="action-link action-edit" 
                               aria-label="Edit user <?= htmlspecialchars($user['full_name']) ?>">
                               Edit
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center; padding: 15px; color: #730000;">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
