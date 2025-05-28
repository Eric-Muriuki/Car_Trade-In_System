<?php
session_start();
require_once '../includes/db-connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch total listed cars
$sql_cars = "SELECT COUNT(*) AS total_cars FROM cars WHERE dealer_id = ?";
$stmt = $conn->prepare($sql_cars);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$total_cars = $result->fetch_assoc()['total_cars'];

// Fetch total trade-in requests sent to this dealer
$sql_trades = "SELECT COUNT(*) AS total_requests FROM trade_offers WHERE dealer_id = ?";
$stmt = $conn->prepare($sql_trades);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$total_requests = $result->fetch_assoc()['total_requests'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dealer Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      max-width: 960px;
      margin: 40px auto;
      padding: 20px;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .card {
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    .card h3 {
      margin-bottom: 10px;
    }
    .card p {
      font-size: 2rem;
      font-weight: bold;
      color: #333;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Welcome to Your Dashboard</h2>
    <div class="dashboard">
      <div class="card">
        <h3>Total Cars Listed</h3>
        <p><?= $total_cars ?></p>
      </div>
      <div class="card">
        <h3>Trade-In Requests</h3>
        <p><?= $total_requests ?></p>
      </div>
    </div>
  </div>

</body>
</html>
