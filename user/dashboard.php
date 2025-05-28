<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch current car
$carQuery = $conn->prepare("SELECT * FROM cars WHERE owner_id = ?");
$carQuery->bind_param("i", $userId);
$carQuery->execute();
$carResult = $carQuery->get_result();
$car = $carResult->fetch_assoc();

// Fetch incoming offers
$offerQuery = $conn->prepare("SELECT COUNT(*) AS offer_count FROM offers WHERE dealer_id = ? AND status = 'Pending'");
$offerQuery->bind_param("i", $userId);
$offerQuery->execute();
$offerResult = $offerQuery->get_result();
$offerData = $offerResult->fetch_assoc();

// Fetch active trades
$tradeQuery = $conn->prepare("SELECT COUNT(*) AS trade_count FROM trades WHERE (user_id = ? OR dealer_id = ?) AND status != 'Completed'");
$tradeQuery->bind_param("ii", $userId, $userId);
$tradeQuery->execute();
$tradeResult = $tradeQuery->get_result();
$tradeData = $tradeResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - Car Trade-In System</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      color: #333;
      border-bottom: 2px solid #eee;
      padding-bottom: 10px;
    }

    .dashboard-card {
      background: #f9f9f9;
      padding: 15px 20px;
      margin: 20px 0;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }

    .dashboard-card h3 {
      margin: 0;
      font-size: 20px;
      color: #2c3e50;
    }

    .dashboard-card p {
      font-size: 16px;
      color: #555;
    }

    .nav-link {
      display: inline-block;
      margin-right: 15px;
      color: #3498db;
      text-decoration: none;
    }

    .nav-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Welcome to Your Dashboard</h2>

    <div class="dashboard-card">
      <h3>My Listed Car</h3>
      <?php if ($car): ?>
        <p><strong>Make:</strong> <?= htmlspecialchars($car['make']) ?></p>
        <p><strong>Model:</strong> <?= htmlspecialchars($car['model']) ?></p>
        <p><strong>Year:</strong> <?= htmlspecialchars($car['year']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($car['status']) ?></p>
        <a href="my_car.php" class="nav-link">Manage Car</a>
      <?php else: ?>
        <p>You have not listed any car yet.</p>
        <a href="my_car.php" class="nav-link">List My Car</a>
      <?php endif; ?>
    </div>

    <div class="dashboard-card">
      <h3>Incoming Offers</h3>
      <p>You have <strong><?= $offerData['offer_count'] ?></strong> new trade offer(s).</p>
      <a href="trade_offers.php" class="nav-link">View Offers</a>
    </div>

    <div class="dashboard-card">
      <h3>Active Trades</h3>
      <p>You are involved in <strong><?= $tradeData['trade_count'] ?></strong> ongoing trade(s).</p>
      <a href="trades.php" class="nav-link">View Trades</a>
    </div>

    <div style="margin-top: 30px;">
      <a href="messages.php" class="nav-link">Messages</a>
      <a href="documents.php" class="nav-link">Documents</a>
      <a href="finance.php" class="nav-link">Finance Options</a>
      <a href="profile.php" class="nav-link">Profile</a>
      <a href="logout.php" class="nav-link">Logout</a>
    </div>
  </div>
</body>
</html>
