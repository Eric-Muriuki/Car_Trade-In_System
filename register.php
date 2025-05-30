<?php
// register.php - User Registration Page
include('includes/db_connect.php');

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $password  = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm   = mysqli_real_escape_string($conn, $_POST['confirm']);

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (full_name, email, phone, password) VALUES ('$full_name', '$email', '$phone', '$hashed')";
            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - SwapRide Kenya</title>
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

    .success-msg {
      background-color: #d4edda;
      color: #155724;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      text-align: center;
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
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="active">Register</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Registration Form -->
<section class="form-section">
  <div class="container">
    <h2>Create Your Account</h2>

    <?php if ($success): ?>
      <div class="success-msg"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="post" class="form-box">
      <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>
      </div>

      <div class="form-group">
        <label>Email Address:</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Phone Number:</label>
        <input type="text" name="phone" required>
      </div>

      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm" required>
      </div>

      <button type="submit" class="btn-submit">Register</button>
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
