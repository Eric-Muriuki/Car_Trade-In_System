<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

// Total users from 'users' table
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($sql_users);
$total_users = $result->fetch_assoc()['total_users'] ?? 0;

// Total dealers from 'dealers' table
$sql_dealers = "SELECT COUNT(*) AS total_dealers FROM dealers";
$result = $conn->query($sql_dealers);
$total_dealers = $result->fetch_assoc()['total_dealers'] ?? 0;

// Completed trades
$sql_trades = "SELECT COUNT(*) AS total_trades FROM offers WHERE status = 'Accepted'";
$result = $conn->query($sql_trades);
$total_trades = $result->fetch_assoc()['total_trades'] ?? 0;

// Earnings from accepted trades
$sql_earnings = "SELECT SUM(offer_price) AS total_earnings FROM offers WHERE status = 'Accepted'";
$result = $conn->query($sql_earnings);
$total_earnings = $result->fetch_assoc()['total_earnings'] ?? 0;
$total_earnings = number_format($total_earnings, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #212529;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }
        .navbar a:hover {
            color: #ffc107;
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
        }
        .nav-title {
            font-size: 1.4rem;
            font-weight: bold;
        }
        .container {
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .stat-box {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .stat-box h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: #555;
        }
        .stat-box p {
            font-size: 2.2rem;
            font-weight: bold;
            margin: 0;
        }
        .users { color: #007bff; }
        .dealers { color: #28a745; }
        .trades { color: #ffc107; }
        .earnings { color: #dc3545; }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-title">
        <i class="fas fa-shield-alt"></i> Admin Panel
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="dealers.php">Dealers</a>
        <a href="cars.php">Listings</a>
        <a href="trades.php">Trade Logs</a>
        <a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>Admin Dashboard</h2>
    <div class="stats-grid">
        <div class="stat-box users">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        <div class="stat-box dealers">
            <h3>Total Dealers</h3>
            <p><?= $total_dealers ?></p>
        </div>
        <div class="stat-box trades">
            <h3>Completed Trades</h3>
            <p><?= $total_trades ?></p>
        </div>
        <div class="stat-box earnings">
            <h3>Total Earnings (KES)</h3>
            <p>KES <?= $total_earnings ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
