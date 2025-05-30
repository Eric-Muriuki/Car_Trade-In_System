<?php
// cars.php - Browse Cars Page
session_start();
include('includes/db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Browse Cars - SwapRide Kenya</title>

  <!-- Font Awesome -->
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
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Header */
    .header {
      background-color: var(--whiteish);
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

    /* Cars Section */
    .cars-section {
      padding: 50px 0;
    }

    .cars-section h2 {
      text-align: center;
      color: var(--red-deep);
      margin-bottom: 30px;
      font-size: 2rem;
    }

    .cars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 25px;
    }

    .car-card {
      background-color: var(--whiteish);
      border: 1px solid var(--red-soft);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px var(--shadow);
      transition: transform 0.3s;
    }

    .car-card:hover {
      transform: scale(1.02);
    }

    .car-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 2px solid var(--red-soft);
    }

    .car-details {
      padding: 15px;
    }

    .car-details h3 {
      font-size: 1.2rem;
      color: var(--red-dark);
      margin-bottom: 10px;
    }

    .car-details p {
      margin: 5px 0;
      font-size: 0.95rem;
    }

    .car-details .price {
      font-size: 1.1rem;
      font-weight: bold;
      color: var(--red-primary);
    }

    /* Footer */
    .footer {
      background-color: var(--whiteish);
      color: var(--blue-dark);
      text-align: center;
      padding: 20px;
      box-shadow: 0 -2px 8px var(--shadow);
      margin-top: auto;
      font-size: 0.9rem;
    }

    /* Responsive */
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
          <li><a href="cars.php" class="active">Browse Cars</a></li>
          <li><a href="how_it_works.php">How It Works</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php" class="btn">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Cars Section -->
  <section class="cars-section">
    <div class="container">
      <h2>Available Cars for Trade-In</h2>

      <div class="cars-grid">
        <?php
        $sql = "SELECT * FROM cars ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="car-card">';
            echo '<img src="uploads/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["make"]) . '">';
            echo '<div class="car-details">';
            echo '<h3>' . htmlspecialchars($row["make"]) . ' ' . htmlspecialchars($row["model"]) . '</h3>';
            echo '<p><strong>Year:</strong> ' . htmlspecialchars($row["year"]) . '</p>';
            echo '<p><strong>Condition:</strong> ' . htmlspecialchars($row["condition"]) . '</p>';
            echo '<p><strong>Location:</strong> ' . htmlspecialchars($row["location"]) . '</p>';
            echo '<p class="price">KES ' . number_format($row["price"]) . '</p>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo "<p>No cars available at the moment. Please check back later.</p>";
        }
        ?>
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
