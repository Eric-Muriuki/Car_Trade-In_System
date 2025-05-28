<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
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

 <style>
        /* Basic responsive navbar styling */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        <a href="reports.php">Reports</a>
        <a href="messages.php">Support</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container" style="max-width: 960px; margin: 40px auto; padding: 20px;">
    <h2>Admin Dashboard</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap: 20px; margin-top: 30px;">
        <div style="background:#f0f4f8; padding: 20px; border-radius: 8px; text-align:center; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h3>Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #007bff;"><?= $total_users ?></p>
        </div>
        <div style="background:#f0f4f8; padding: 20px; border-radius: 8px; text-align:center; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h3>Total Dealers</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #28a745;"><?= $total_dealers ?></p>
        </div>
        <div style="background:#f0f4f8; padding: 20px; border-radius: 8px; text-align:center; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h3>Completed Trades</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #ffc107;"><?= $total_trades ?></p>
        </div>
        <div style="background:#f0f4f8; padding: 20px; border-radius: 8px; text-align:center; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <h3>Total Earnings (KES)</h3>
            <p style="font-size: 2rem; font-weight: bold; color: #dc3545;">KES <?= $total_earnings ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>