<?php
// how_it_works.php - How the Car Trade-In System Works
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>How It Works - SwapRide Kenya</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
          <li><a href="how_it_works.php" class="active">How It Works</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php" class="btn">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Section -->
  <section class="how-it-works">
    <div class="container">
      <h2>How It Works</h2>
      <p class="intro">Trading in your car has never been easier. Here's a step-by-step breakdown of how SwapRide Kenya works:</p>

      <div class="steps">
        <div class="step">
          <i class="fas fa-user-plus"></i>
          <h3>1. Create an Account</h3>
          <p>Sign up as an individual or a licensed dealer to access our platform features.</p>
        </div>
        <div class="step">
          <i class="fas fa-car-side"></i>
          <h3>2. List Your Vehicle</h3>
          <p>Upload your car's details including photos, model, year, and condition for trade-in or sale.</p>
        </div>
        <div class="step">
          <i class="fas fa-search"></i>
          <h3>3. Receive Offers</h3>
          <p>Get trade-in offers from verified dealers or individuals based on your car's market value.</p>
        </div>
        <div class="step">
          <i class="fas fa-exchange-alt"></i>
          <h3>4. Accept & Swap</h3>
          <p>Choose the best deal, arrange a meet-up or delivery, and complete the trade securely.</p>
        </div>
        <div class="step">
          <i class="fas fa-file-signature"></i>
          <h3>5. Paperwork Support</h3>
          <p>We guide both parties through the vehicle transfer documentation process.</p>
        </div>
      </div>
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
