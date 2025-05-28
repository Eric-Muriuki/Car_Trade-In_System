<?php
// contact.php - Contact Page for SwapRide Kenya
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - SwapRide Kenya</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
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
          <li><a href="contact.php" class="active">Contact</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php" class="btn">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="container">
      <h2>Contact Us</h2>
      <p class="intro">Have a question or feedback? Reach out to us below.</p>

      <div class="contact-content">
        <form action="#" method="POST" class="contact-form">
          <div class="form-group">
            <label for="name"><i class="fas fa-user"></i> Your Name</label>
            <input type="text" id="name" name="name" required/>
          </div>
          <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
            <input type="email" id="email" name="email" required/>
          </div>
          <div class="form-group">
            <label for="subject"><i class="fas fa-tag"></i> Subject</label>
            <input type="text" id="subject" name="subject" required/>
          </div>
          <div class="form-group">
            <label for="message"><i class="fas fa-comment-dots"></i> Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>

        <div class="contact-details">
          <h3>Contact Info</h3>
          <p><i class="fas fa-map-marker-alt"></i> Nairobi, Kenya</p>
          <p><i class="fas fa-phone-alt"></i> +254 712 345 678</p>
          <p><i class="fas fa-envelope"></i> support@swapride.co.ke</p>
          <p><i class="fas fa-clock"></i> Mon - Fri: 9:00 AM - 5:00 PM</p>
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
