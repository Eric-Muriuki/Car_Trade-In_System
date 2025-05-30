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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --whiteish: #FFFFFA;
      --red-soft: #FF9B9B;
      --blue-dark: #00232A;
      --red-deep: #730000;
      --shadow: rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--whiteish);
      color: var(--blue-dark);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .container {
      width: 90%;
      max-width: 1100px;
      margin: 0 auto;
    }

    header {
      background: var(--whiteish);
      padding: 20px 0;
      box-shadow: 0 2px 8px var(--shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--red-deep);
    }

    nav {
      position: relative;
    }

    .nav-links {
      display: flex;
      list-style: none;
      gap: 20px;
      flex-wrap: wrap;
    }

    .nav-links a {
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 6px;
      font-weight: 600;
      color: var(--red-dark);
      transition: 0.3s;
    }

    .nav-links a:hover,
    .nav-links a.active {
      background-color: var(--red-primary);
      color: var(--whiteish);
    }

    .nav-links .btn {
      background-color: var(--red-dark);
      color: var(--whiteish);
      border-radius: 8px;
    }

    .nav-toggle-btn {
      display: none;
      font-size: 1.5rem;
      background: var(--red-primary);
      color: var(--whiteish);
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
    }

    /* Contact Section */
    .contact-section {
      padding: 50px 0;
      flex: 1;
    }

    .contact-section h2 {
      text-align: center;
      color: var(--red-deep);
      margin-bottom: 10px;
      font-size: 2rem;
    }

    .contact-section .intro {
      text-align: center;
      color: var(--blue-dark);
      margin-bottom: 40px;
      font-size: 1rem;
    }

    .contact-content {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
    }

    .contact-form, .contact-details {
      flex: 1 1 300px;
      background: var(--whiteish);
      padding: 20px;
      border: 1px solid var(--red-soft);
      border-radius: 12px;
      box-shadow: 0 4px 12px var(--shadow);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: var(--red-dark);
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid var(--red-soft);
      border-radius: 8px;
      font-size: 1rem;
      background: #fff;
    }

    .btn-submit {
      background: var(--red-dark);
      color: var(--whiteish);
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-submit:hover {
      background: var(--red-primary);
    }

    .contact-details h3 {
      margin-bottom: 15px;
      color: var(--red-deep);
    }

    .contact-details p {
      margin: 8px 0;
      font-size: 0.95rem;
    }

    .contact-details i {
      color: var(--red-primary);
      margin-right: 8px;
    }

    footer.footer {
      background-color: var(--whiteish);
      color: var(--blue-dark);
      text-align: center;
      padding: 20px;
      box-shadow: 0 -2px 8px var(--shadow);
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .nav-links {
        display: none;
        flex-direction: column;
        background: var(--whiteish);
        position: absolute;
        right: 0;
        top: 60px;
        padding: 15px;
        box-shadow: 0 4px 12px var(--shadow);
        border-radius: 10px;
        z-index: 1001;
      }

      .nav-links.show {
        display: flex;
      }

      .nav-toggle-btn {
        display: block;
      }

      .contact-content {
        flex-direction: column;
      }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const toggleBtn = document.createElement('button');
      toggleBtn.classList.add('nav-toggle-btn');
      toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
      const nav = document.querySelector('nav');
      const navLinks = document.querySelector('.nav-links');

      toggleBtn.addEventListener('click', () => {
        navLinks.classList.toggle('show');
      });

      nav.insertBefore(toggleBtn, navLinks);
    });
  </script>
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
