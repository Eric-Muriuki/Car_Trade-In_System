<?php
session_start();
require_once '../includes/db-connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch total users
$sql_users = "SELECT COUNT(*) AS total_users FROM users WHERE user_type = 'user'";
$result = $conn->query($sql_users);
$total_users = $result->fetch_assoc()['total_users'] ?? 0;

// Fetch total dealers
$sql_dealers = "SELECT COUNT(*) AS total_dealers FROM users WHERE user_type = 'dealer'";
$result = $conn->query($sql_dealers);
$total_dealers = $result->fetch_assoc()['total_dealers'] ?? 0;

// Fetch total trades (completed trades)
$sql_trades = "SELECT COUNT(*) AS total_trades FROM trade_offers WHERE status = 'Accepted'";
$result = $conn->query($sql_trades);
$total_trades = $result->fetch_assoc()['total_trades'] ?? 0;

// Fetch total earnings (sum of cash difference on accepted trades)
$sql_earnings = "SELECT SUM(offer_cash_difference) AS total_earnings FROM trade_offers WHERE status = 'Accepted'";
$result = $conn->query($sql_earnings);
$total_earnings = $result->fetch_assoc()['total_earnings'] ?? 0;
$total_earnings = number_format($total_earnings, 2);

?>

<?php include '../includes/header.php'; ?>

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
