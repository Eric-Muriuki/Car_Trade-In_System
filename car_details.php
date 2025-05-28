<?php
// car_details.php - Show specific car information
include('db-connect.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid car ID.";
    exit;
}

$car_id = intval($_GET['id']);
$query = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Car not found.";
    exit;
}

$car = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> - Car Details | SwapRide Kenya</title>
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
        <li><a href="contact.php">Contact</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Car Detail Section -->
<section class="car-detail-section">
  <div class="container">
    <div class="car-detail-box">
      <img src="uploads/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['make']) ?>" class="car-detail-img"/>

      <div class="car-detail-content">
        <h2><?= htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) ?> (<?= $car['year'] ?>)</h2>
        <p><strong>Price:</strong> KES <?= number_format($car['price']) ?></p>
        <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
        <p><strong>Condition:</strong> <?= htmlspecialchars($car['condition']) ?></p>
        <p><strong>Fuel Type:</strong> <?= htmlspecialchars($car['fuel_type']) ?></p>
        <p><strong>Transmission:</strong> <?= htmlspecialchars($car['transmission']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($car['location']) ?></p>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($car['description'])) ?></p>

        <a href="trade_request.php?car_id=<?= $car['id'] ?>" class="btn-submit">Request Trade-In</a>
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
