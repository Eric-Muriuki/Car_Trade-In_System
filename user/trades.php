<?php
// user/trades.php - View accepted or completed trades
session_start();
include('../includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch accepted or completed trades where user is involved
$query = "
SELECT o.*, 
       u.full_name AS user_full_name,
       d.business_name AS dealer_name,
       c1.make AS user_car_make, c1.model AS user_car_model, c1.year AS user_car_year, c1.image AS user_car_image,
       c2.make AS dealer_car_make, c2.model AS dealer_car_model, c2.year AS dealer_car_year, c2.image AS dealer_car_image
FROM offers o
JOIN trades t ON o.trade_id = t.id
JOIN users u ON t.user_id = u.id
JOIN dealers d ON t.dealer_id = d.id
JOIN cars c1 ON t.user_car_id = c1.id
LEFT JOIN cars c2 ON t.dealer_car_id = c2.id
WHERE (t.user_id = $user_id)
AND o.status IN ('accepted', 'completed')
ORDER BY o.created_at DESC
";



$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Accepted Trades | SwapRide Kenya</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="trade_offers.php">Trade Offers</a></li>
        <li><a href="trades.php" class="active">Accepted Trades</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Trades Section -->
<section class="section">
  <div class="container">
    <h2>My Accepted Trades</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="trade-list">
        <?php while ($trade = mysqli_fetch_assoc($result)): ?>
          <div class="trade-box">
            <div class="trade-cars">
              <!-- Offered Car -->
              <div class="car">
                <h4>Offered Car</h4>
                <img src="../uploads/<?= htmlspecialchars($trade['offered_image']) ?>" alt="Offered Car">
                <p><?= htmlspecialchars($trade['offered_make']) ?> <?= htmlspecialchars($trade['offered_model']) ?> (<?= $trade['offered_year'] ?>)</p>
                <p><strong>By:</strong> <?= htmlspecialchars($trade['from_user_name']) ?></p>
              </div>

              <!-- Requested Car -->
              <div class="car">
                <h4>Requested Car</h4>
                <img src="../uploads/<?= htmlspecialchars($trade['requested_image']) ?>" alt="Requested Car">
                <p><?= htmlspecialchars($trade['requested_make']) ?> <?= htmlspecialchars($trade['requested_model']) ?> (<?= $trade['requested_year'] ?>)</p>
                <p><strong>By:</strong> <?= htmlspecialchars($trade['to_user_name']) ?></p>
              </div>
            </div>

            <div class="trade-info">
              <p><strong>Status:</strong> <span class="status <?= $trade['status'] ?>"><?= ucfirst($trade['status']) ?></span></p>
              <p><strong>Date:</strong> <?= date('d M Y', strtotime($trade['created_at'])) ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>No accepted or completed trades found.</p>
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
