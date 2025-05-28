<?php
// dealer/login.php - Dealer Login Page
session_start();
include('../includes/db_connect.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch dealer info
    $stmt = $conn->prepare("SELECT id, password, approved FROM dealers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($dealer_id, $hashed_password, $approved);
        $stmt->fetch();

        if (!$approved) {
            $error = "Your registration is pending approval.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['dealer_id'] = $dealer_id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Dealer not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dealer Login | SwapRide Kenya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php" class="active">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container">
        <h2>Dealer Login</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
