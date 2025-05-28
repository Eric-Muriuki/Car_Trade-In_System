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
        // Fetch cars from database
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
