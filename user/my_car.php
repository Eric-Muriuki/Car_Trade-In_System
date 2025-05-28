<?php
// user/my_car.php - User's Car Profile Page
session_start();
include('../includes/db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Fetch user's submitted cars
$query = "SELECT * FROM cars WHERE owner_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Car Profile | SwapRide Kenya</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="trade_requests.php">My Requests</a></li>
        <li><a href="my_car.php" class="active">My Car</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- My Car Profile Section -->
<section class="mycar-section">
  <div class="container">
    <h2>My Car Profile</h2>
    <a href="submit_car.php" class="btn-submit">Submit New Car</a>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="car-list">
        <?php while ($car = mysqli_fetch_assoc($result)): ?>
          <div class="car-box">
            <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" alt="Car Image">
            <div class="car-info">
              <h3><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)</h3>
              <p><strong>Price:</strong> KES <?= number_format($car['price']) ?></p>
              <p><strong>Condition:</strong> <?= htmlspecialchars($car['car_condition']) ?></p>
              <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
              <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn">Edit</a>
              <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this car?');">Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>You have not submitted any cars yet. <a href="submit_car.php">Submit one now</a>.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
