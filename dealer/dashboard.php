<?php
session_start();
require_once '../includes/db_connect.php';

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
$sql_trades = "SELECT COUNT(*) AS total_requests FROM offers WHERE dealer_id = ?";
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
    /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
    color: #333;
    line-height: 1.6;
}

/* Container */
.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

/* Header */
.dashboard-header {
    background-color: #003049;
    padding: 15px 0;
    color: white;
    border-bottom: 3px solid #f77f00;
}

.dashboard-header .logo {
    font-size: 1.8rem;
    margin-bottom: 10px;
}

.nav-dashboard {
    margin-top: 10px;
}

.nav-links {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.nav-links li a {
    color: #fff;
    text-decoration: none;
    padding: 8px 14px;
    background-color: #003049;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.nav-links li a:hover,
.nav-links li a.active {
    background-color: #f77f00;
    color: #fff;
}

/* Main Content */
.main-content {
    padding: 30px 20px;
    background-color: #ffffff;
    border-radius: 8px;
    margin: 20px auto;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Forms */
.form-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
    max-width: 500px;
    margin: auto;
}

.form-card label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

.form-card input,
.form-card textarea,
.form-card select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.form-card .btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #003049;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-card .btn:hover {
    background-color: #f77f00;
}

/* Tables */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th,
.table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.table th {
    background-color: #003049;
    color: white;
}

.table tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Error/Message */
.error {
    color: red;
    margin: 10px 0;
}

.message {
    color: green;
    margin: 10px 0;
}

/* Footer */
.footer {
    background-color: #003049;
    color: #fff;
    padding: 15px 0;
    text-align: center;
    margin-top: 40px;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-links {
        flex-direction: column;
    }

    .form-card {
        padding: 15px;
    }

    .main-content {
        padding: 20px;
    }
}

  </style>
</head>
<body> 
  <header class="dashboard-header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya - Dealer Panel</h1>
        <nav class="nav-dashboard">
            <ul class="nav-links">
                <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                <li><a href="add_car.php" class="<?= $current_page == 'add_car.php' ? 'active' : '' ?>">Add New Car</a></li>
                <li><a href="my_cars.php" class="<?= $current_page == 'my_cars.php' ? 'active' : '' ?>">My Inventory</a></li>
                <li><a href="trade_requests.php" class="<?= $current_page == 'trade_requests.php' ? 'active' : '' ?>">Trade-In Requests</a></li>
                <li><a href="send_offer.php" class="<?= $current_page == 'send_offer.php' ? 'active' : '' ?>">Send Offer</a></li>
                <li><a href="manage_offers.php" class="<?= $current_page == 'manage_offers.php' ? 'active' : '' ?>">Manage Offers</a></li>
                <li><a href="messages.php" class="<?= $current_page == 'messages.php' ? 'active' : '' ?>">Messages</a></li>
                <li><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">Profile</a></li>
                <li><a href="logout.php" class="<?= $current_page == 'logout.php' ? 'active' : '' ?>">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

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
