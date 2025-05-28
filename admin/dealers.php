<?php
// Connect to database
require_once '../includes/db_connect.php'; // Adjust path as needed

// Handle approval request
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    $conn->query("UPDATE dealers SET approved = TRUE WHERE id = $approve_id");
    header("Location: dealers.php"); // Redirect to avoid resubmission
    exit;
}

// Fetch all dealers
$result = $conn->query("SELECT id, business_name, contact_person, email, phone, approved FROM dealers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Dealers List</title>
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .btn-approve {
        background-color: #4CAF50;
        color: white;
        padding: 6px 12px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-approve:hover {
        background-color: #45a049;
    }
</style>
</head>
<body>
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

<h2>Dealers List</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Business Name</th>
            <th>Contact Person</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Approved</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['approved'] ? "Yes" : "No"; ?></td>
                <td>
                    <?php if (!$row['approved']): ?>
                        <a 
                          href="dealers.php?approve_id=<?php echo $row['id']; ?>" 
                          class="btn-approve"
                          onclick="return confirm('Are you sure you want to approve this dealer?');"
                        >Approve</a>
                    <?php else: ?>
                        <!-- Already approved, no button -->
                        &mdash;
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7">No dealers found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
