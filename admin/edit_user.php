<?php
session_start();
require_once '../includes/db_connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Validate and get user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');

    // Basic validation
    if (empty($full_name) || empty($email) || empty($user_type)) {
        $error = "Full Name, Email and User Type are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update user
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, user_type = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $user_type, $user_id);
        if ($stmt->execute()) {
            $success = "User updated successfully.";
        } else {
            $error = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch user data for initial form or after update
$stmt = $conn->prepare("SELECT full_name, email, phone, user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone, $user_type);
if (!$stmt->fetch()) {
    // User not found
    $stmt->close();
    header("Location: users.php");
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit User - Admin Panel</title>
<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #FFFFFA;
    color: #00232A;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  .navbar {
    background: linear-gradient(90deg, #FE0000 0%, #AF0000 100%);
    padding: 14px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #FFFFFA;
    flex-wrap: wrap;
  }
  .nav-title {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 1px;
    user-select: none;
  }
  .nav-links {
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
    margin-top: 8px;
  }
  .nav-links a {
    color: #FFFFFA;
    text-decoration: none;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background-color 0.3s ease, color 0.3s ease;
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
    background-color: #AF0000;
    color: #FFFFFA;
  }
  .container {
    flex: 1;
    max-width: 600px;
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
  form {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  label {
    font-weight: 600;
    margin-bottom: 6px;
  }
  input[type="text"],
  input[type="email"],
  select {
    padding: 10px 14px;
    border: 1.5px solid #AF0000;
    border-radius: 6px;
    font-size: 1rem;
    color: #00232A;
    background-color: #FFFFFA;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="email"]:focus,
  select:focus {
    border-color: #FE0000;
    outline: none;
  }
  .btn {
    padding: 12px;
    background-color: #FE0000;
    color: #FFFFFA;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  .btn:hover {
    background-color: #AF0000;
  }
  .message {
    text-align: center;
    padding: 12px;
    border-radius: 8px;
  }
  .error {
    background-color: #FF9B9B;
    color: #730000;
    font-weight: 700;
    margin-bottom: 12px;
  }
  .success {
    background-color: #00232A;
    color: #FF9B9B;
    font-weight: 700;
    margin-bottom: 12px;
  }
  .back-link {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    color: #00232A;
    font-weight: 600;
    border: 1.5px solid #AF0000;
    padding: 8px 14px;
    border-radius: 6px;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .back-link:hover {
    background-color: #AF0000;
    color: #FFFFFA;
  }

  @media (max-width: 480px) {
    .container {
      margin: 20px 12px;
      padding: 20px 16px;
    }
  }
</style>
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
  <h2>Edit User</h2>

  <?php if ($error): ?>
    <div class="message error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="message success" role="alert"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <label for="full_name">Full Name <span aria-hidden="true" style="color: #FE0000;">*</span></label>
    <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($full_name) ?>" />

    <label for="email">Email <span aria-hidden="true" style="color: #FE0000;">*</span></label>
    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>" />

    <label for="phone">Phone</label>
    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" />

    <label for="user_type">User Type <span aria-hidden="true" style="color: #FE0000;">*</span></label>
    <select id="user_type" name="user_type" required>
      <option value="user" <?= $user_type === 'user' ? 'selected' : '' ?>>User</option>
      <option value="dealer" <?= $user_type === 'dealer' ? 'selected' : '' ?>>Dealer</option>
      <option value="admin" <?= $user_type === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit" class="btn">Update User</button>
  </form>

  <a href="users.php" class="back-link" aria-label="Back to Users List">&larr; Back to Users</a>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
