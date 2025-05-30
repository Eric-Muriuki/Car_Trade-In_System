<?php
session_start();
require_once '../includes/db_connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Trade Logs - Admin Panel</title>
    <style>
        /* Root color palette */
        :root {
            --red-bright: #FE0000;
            --red-dark: #AF0000;
            --white-cream: #FFFFFA;
            --red-light: #FF9B9B;
            --blue-dark: #00232A;
            --red-deep: #730000;
            --gray-light: #f0f0f0;
            --gray-dark: #343a40;
            --gray-medium: #6c757d;
        }

        /* Reset and base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--white-cream);
            color: var(--blue-dark);
        }
        a {
            text-decoration: none;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, var(--red-dark), var(--red-deep));
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--white-cream);
            flex-wrap: wrap;
        }
        .nav-title {
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-title i {
            color: var(--white-cream);
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }
        .nav-links a {
            color: var(--white-cream);
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: var(--red-light);
            color: var(--red-deep);
        }
        .nav-links a.logout {
            background-color: var(--red-bright);
            color: var(--white-cream);
        }
        .nav-links a.logout:hover {
            background-color: var(--red-deep);
            color: var(--white-cream);
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px 40px;
        }

        h2 {
            margin-bottom: 20px;
            color: var(--red-deep);
            text-align: center;
            font-weight: 700;
        }

        /* Table styles */
        .table-wrapper {
            overflow-x: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            background-color: var(--white-cream);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }
        thead {
            background: linear-gradient(90deg, var(--red-deep), var(--red-dark));
            color: var(--white-cream);
            font-weight: 600;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid var(--gray-light);
            text-align: left;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        tbody tr:nth-child(even) {
            background-color: var(--red-light);
        }
        tbody tr:hover {
            background-color: var(--red-bright);
            color: var(--white-cream);
            transition: background-color 0.3s ease;
        }

        /* Status colors */
        .status {
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 18px;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }
        .status.Pending {
            background-color: #FF9B9B;
            color: var(--red-deep);
        }
        .status.Accepted {
            background-color: #28a745;
            color: var(--white-cream);
        }
        .status.Rejected {
            background-color: var(--red-bright);
            color: var(--white-cream);
        }
        .status.Completed {
            background-color: #007bff;
            color: var(--white-cream);
        }
        .status.Default {
            background-color: var(--gray-medium);
            color: var(--white-cream);
        }

        /* Responsive typography and layout */
        @media (max-width: 992px) {
            .nav-links {
                justify-content: center;
                gap: 8px;
            }
            .nav-title {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
            table {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 6px;
                margin-top: 10px;
            }
            table {
                min-width: 600px;
                font-size: 0.8rem;
            }
            th, td {
                padding: 8px 10px;
            }
        }
    </style>
    <script>
        // Optional: Simple script to add responsiveness to table headers on small devices
        document.addEventListener("DOMContentLoaded", () => {
            const tableWrapper = document.querySelector('.table-wrapper');
            if (window.innerWidth < 600 && tableWrapper) {
                tableWrapper.style.overflowX = 'auto';
            }
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
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
        <a href="trades.php" class="active">Trade Logs</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>Trade Logs</h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Trade ID</th>
                <th>User</th>
                <th>Dealer</th>
                <th>Car</th>
                <th>Price (Ksh)</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($trade = $result->fetch_assoc()): ?>
                <?php 
                    $status_class = in_array($trade['status'], ['Pending', 'Accepted', 'Rejected', 'Completed']) 
                        ? $trade['status'] : 'Default';
                ?>
                <tr>
                    <td><?= htmlspecialchars($trade['trade_id']) ?></td>
                    <td><?= htmlspecialchars($trade['user_name']) ?></td>
                    <td><?= htmlspecialchars($trade['dealer_name']) ?></td>
                    <td><?= htmlspecialchars($trade['make']) ?> <?= htmlspecialchars($trade['model']) ?> (<?= htmlspecialchars($trade['year']) ?>)</td>
                    <td><?= number_format($trade['price']) ?></td>
                    <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($trade['status']) ?></span></td>
                    <td><?= date('d M Y, H:i', strtotime($trade['created_at'])) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($trade['updated_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <p style="text-align:center; font-size:1.1rem; color: var(--red-deep);">No trades found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
