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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --red-main: #FE0000;
      --red-dark: #AF0000;
      --red-deep: #730000;
      --whiteish: #FFFFFA;
      --red-soft: #FF9B9B;
      --blue-deep: #00232A;
      --shadow: rgba(0,0,0,0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--whiteish);
      color: var(--blue-deep);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .container {
      width: 90%;
      max-width: 1100px;
      margin: auto;
    }

    header {
      background: var(--whiteish);
      padding: 20px 0;
      box-shadow: 0 2px 10px var(--shadow);
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
      transition: 0.3s ease;
    }

    .nav-links a:hover,
    .nav-links a.active {
      background-color: var(--red-main);
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
      background: var(--red-main);
      color: var(--whiteish);
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
    }

    /* How It Works Section */
    .how-it-works {
      padding: 60px 0;
      flex: 1;
    }

    .how-it-works h2 {
      text-align: center;
      color: var(--red-deep);
      font-size: 2.2rem;
      margin-bottom: 20px;
    }

    .how-it-works .intro {
      text-align: center;
      font-size: 1rem;
      color: var(--blue-deep);
      margin-bottom: 40px;
    }

    .steps {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }

    .step {
      background-color: var(--whiteish);
      border: 1px solid var(--red-soft);
      border-radius: 12px;
      box-shadow: 0 4px 12px var(--shadow);
      padding: 30px 20px;
      width: 280px;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .step:hover {
      transform: translateY(-5px);
    }

    .step i {
      font-size: 2.5rem;
      color: var(--red-main);
      margin-bottom: 10px;
    }

    .step h3 {
      color: var(--red-dark);
      font-size: 1.2rem;
      margin-bottom: 10px;
    }

    .step p {
      font-size: 0.95rem;
      color: var(--blue-deep);
    }

    footer.footer {
      background-color: var(--whiteish);
      color: var(--blue-deep);
      text-align: center;
      padding: 20px;
      box-shadow: 0 -2px 8px var(--shadow);
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .nav-links {
        display: none;
        flex-direction: column;
        position: absolute;
        right: 0;
        top: 60px;
        background: var(--whiteish);
        box-shadow: 0 4px 12px var(--shadow);
        padding: 15px;
        border-radius: 10px;
        z-index: 1001;
      }

      .nav-links.show {
        display: flex;
      }

      .nav-toggle-btn {
        display: block;
      }

      .steps {
        flex-direction: column;
        align-items: center;
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
