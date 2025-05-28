<?php
session_start();
require_once '../includes/db_connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all trades with user, dealer, car info
$sql = "SELECT t.id AS trade_id, 
               t.status, 
               t.created_at, 
               t.updated_at,
               u.full_name AS user_name, 
               d.business_name AS dealer_name, 
               c.make, c.model, c.year, c.price
        FROM trades t
        JOIN users u ON t.user_id = u.id
        JOIN dealers d ON t.dealer_id = d.id
        JOIN cars c ON t.user_car_id = c.id
        ORDER BY t.created_at DESC";

$result = $conn->query($sql);
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

<div class="container" style="max-width:1100px; margin: 40px auto; padding: 20px;">
    <h2>Trade Logs</h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background-color: #343a40; color: #fff;">
                <th style="padding: 10px; border: 1px solid #ddd;">Trade ID</th>
                <th style="padding: 10px; border: 1px solid #ddd;">User</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Dealer</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Car</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Price (Ksh)</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Created At</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Last Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($trade = $result->fetch_assoc()): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($trade['trade_id']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($trade['user_name']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($trade['dealer_name']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?= htmlspecialchars($trade['make']) ?> <?= htmlspecialchars($trade['model']) ?> (<?= htmlspecialchars($trade['year']) ?>)
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($trade['price']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: 
                        <?php
                        switch($trade['status']){
                            case 'Pending': echo '#ffc107'; break; // yellow
                            case 'Accepted': echo '#28a745'; break; // green
                            case 'Rejected': echo '#dc3545'; break; // red
                            case 'Completed': echo '#007bff'; break; // blue
                            default: echo '#6c757d'; // gray
                        }
                        ?>
                        ">
                        <?= htmlspecialchars($trade['status']) ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y, H:i', strtotime($trade['created_at'])) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y, H:i', strtotime($trade['updated_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No trades found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
