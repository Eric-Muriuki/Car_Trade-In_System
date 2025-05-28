<?php
session_start();
require_once '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Approve a car listing
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    $stmt = $conn->prepare("UPDATE cars SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cars.php");
    exit();
}

// Reject a car listing
if (isset($_GET['reject_id'])) {
    $reject_id = intval($_GET['reject_id']);
    $stmt = $conn->prepare("UPDATE cars SET is_approved = -1 WHERE id = ?");
    $stmt->bind_param("i", $reject_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cars.php");
    exit();
}

// Delete a car listing
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cars.php");
    exit();
}

// Fetch all cars with owner info
$sql = "SELECT cars.id, cars.make, cars.model, cars.year, cars.price, cars.is_approved, cars.created_at,
        users.fullname, users.email
        FROM cars
        JOIN users ON cars.owner_id = users.id
        ORDER BY cars.created_at DESC";

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

<div class="container" style="max-width: 1100px; margin: 40px auto; padding: 20px;">
    <h2>Manage Car Listings</h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background-color: #007bff; color: #fff;">
                <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Make</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Model</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Year</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Price (Ksh)</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Owner</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Listed On</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($car = $result->fetch_assoc()): ?>
                <tr style="background-color: <?= ($car['is_approved'] == 1) ? '#d4edda' : (($car['is_approved'] == -1) ? '#f8d7da' : '#fff3cd') ?>;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['id']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['make']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['model']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['year']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($car['price']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['fullname']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($car['email']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd; color: <?= ($car['is_approved'] == 1) ? 'green' : (($car['is_approved'] == -1) ? 'red' : '#856404') ?>;">
                        <?php 
                            if ($car['is_approved'] == 1) echo "Approved";
                            elseif ($car['is_approved'] == -1) echo "Rejected";
                            else echo "Pending";
                        ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d M Y', strtotime($car['created_at'])) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php if ($car['is_approved'] == 0): ?>
                            <a href="cars.php?approve_id=<?= $car['id'] ?>" style="color: green; text-decoration:none;" onclick="return confirm('Approve this listing?');">Approve</a> | 
                            <a href="cars.php?reject_id=<?= $car['id'] ?>" style="color: red; text-decoration:none;" onclick="return confirm('Reject this listing?');">Reject</a> | 
                        <?php endif; ?>
                        <a href="cars.php?delete_id=<?= $car['id'] ?>" style="color: #dc3545; text-decoration:none;" onclick="return confirm('Delete this listing? This action cannot be undone.');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No car listings found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
