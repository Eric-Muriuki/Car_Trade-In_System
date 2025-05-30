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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --red-main: #FE0000;
      --red-dark: #AF0000;
      --red-light: #FF9B9B;
      --cream: #FFFFFA;
      --navy: #00232A;
      --maroon: #730000;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--cream);
      color: var(--navy);
    }

    .container {
      max-width: 500px;
      margin: 0 auto;
      padding: 2rem;
    }

    .header {
      background: var(--red-main);
      color: var(--cream);
      padding: 1rem 0;
    }

    .header .logo {
      text-align: center;
      font-size: 1.8rem;
      font-weight: bold;
    }

    nav {
      margin-top: 1rem;
      text-align: center;
    }

    .nav-links {
      list-style: none;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      padding: 0;
    }

    .nav-links li a {
      color: var(--cream);
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .nav-links li a:hover,
    .nav-links li a.active {
      background: var(--maroon);
    }

    .form-section {
      padding: 3rem 1rem;
    }

    .form-section h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: var(--red-dark);
    }

    .form-box {
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .btn-submit {
      background: var(--navy);
      color: var(--cream);
      padding: 0.75rem 1.5rem;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-submit:hover {
      background: var(--maroon);
    }

    .error-msg {
      background-color: #ffcccc;
      color: #b20000;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      text-align: center;
    }

    .form-box p {
      text-align: center;
      margin-top: 1rem;
    }

    .form-box a {
      color: var(--red-dark);
      text-decoration: none;
      font-weight: 600;
    }

    .form-box a:hover {
      text-decoration: underline;
    }

    .footer {
      background: var(--navy);
      color: var(--cream);
      text-align: center;
      padding: 1rem 0;
      margin-top: 3rem;
    }

    @media (max-width: 600px) {
      .container {
        padding: 1rem;
      }
    }
  </style>
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
        <li><a href="login.php" class="active">Login</a></li>
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
      <p>Don't have an account? <a href="register.php">Register here</a></p>
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
