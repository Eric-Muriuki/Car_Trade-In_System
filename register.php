<?php
// register.php - User Registration Page
include('includes/db_connect.php');

$success = ""; //to store success message
$error = ""; //to store error message

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name     = mysqli_real_escape_string($conn, $_POST['full_name']); //
    $email    = mysqli_real_escape_string($conn, $_POST['email']); 
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']); // 
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm  = mysqli_real_escape_string($conn, $_POST['confirm']);

    // Validate inputs
    if ($password !== $confirm) {
        $error = "Passwords do not match."; // display error message if passwords do not match
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'"); // query to check if email already exists
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered."; // display error message if email already exists
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT); // hash password
            $sql = "INSERT INTO users (full_name, email, phone, password) VALUES ('$full_name', '$email', '$phone', '$hashed')"; // query to insert new user into database
            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully. You can now <a href='login.php'>login</a>."; // display success message if account created successfully
            } else {
                $error = "Error: " . mysqli_error($conn); // display error message if query fails
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
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="btn active">Register</a></li>
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
        <input type="text" name="name" required>
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
