<?php
// login.php - User Login Page
include('includes/db_connect.php');
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch user record
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: user/dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - SwapRide Kenya</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="cars.php">Browse Cars</a></li>
        <li><a href="how_it_works.php">How It Works</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="login.php" class="btn active">Login</a></li>
        <li><a href="register.php">Register</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Login Form -->
<section class="form-section">
  <div class="container">
    <h2>User Login</h2>

    <?php if ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="post" class="form-box">
      <div class="form-group">
        <label>Email Address:</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="btn-submit">Login</button>
      <p style="margin-top: 15px;">Don't have an account? <a href="register.php">Register here</a></p>
    </form>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?= date("Y") ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
