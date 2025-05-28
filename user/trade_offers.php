<?php
// user/trade_offers.php - View trade offers from dealers/users
session_start();
include('../db-connect.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Fetch trade offers for this user
$query = "SELECT o.*, c.make AS offered_make, c.model AS offered_model, c.year AS offered_year, c.image AS offered_image,
                 u.name AS from_user
          FROM trade_offers o
          JOIN user_cars c ON o.car_id_offered = c.id
          JOIN users u ON o.from_user_id = u.id
          WHERE o.to_user_id = $user_id
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Trade Offers | SwapRide Kenya</title>
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
        <li><a href="trade_offers.php" class="active">Trade Offers</a></li>
        <li><a href="my_car.php">My Car</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Trade Offers Section -->
<section class="section">
  <div class="container">
    <h2>Trade Offers Received</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="offers-list">
        <?php while ($offer = mysqli_fetch_assoc($result)): ?>
          <div class="offer-box">
            <img src="../uploads/<?= htmlspecialchars($offer['offered_image']) ?>" alt="Offered Car Image">
            <div class="offer-info">
              <h3><?= htmlspecialchars($offer['offered_make'] . ' ' . $offer['offered_model']) ?> (<?= $offer['offered_year'] ?>)</h3>
              <p><strong>Offered By:</strong> <?= htmlspecialchars($offer['from_user']) ?></p>
              <p><strong>Message:</strong> <?= htmlspecialchars($offer['message']) ?></p>
              <p><strong>Status:</strong> <?= ucfirst($offer['status']) ?></p>

              <?php if ($offer['status'] == 'pending'): ?>
                <form method="post" action="process_offer.php" style="margin-top: 10px;">
                  <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                  <button type="submit" name="action" value="accept" class="btn">Accept</button>
                  <button type="submit" name="action" value="counter" class="btn btn-secondary">Counter</button>
                </form>
              <?php else: ?>
                <p class="status-msg">You have <?= $offer['status'] ?> this offer.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>No trade offers received yet.</p>
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
