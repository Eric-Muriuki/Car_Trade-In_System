<?php
// Connect to database
require_once '../includes/db_connect.php';

// Handle approval request
if (isset($_GET['approve_id'])) {
    $approve_id = intval($_GET['approve_id']);
    $conn->query("UPDATE dealers SET approved = TRUE WHERE id = $approve_id");
    header("Location: dealers.php");
    exit;
}

// Fetch all dealers
$result = $conn->query("SELECT id, business_name, contact_person, email, phone, approved FROM dealers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dealers List</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #FFFFFA;
        color: #00232A;
    }

    .navbar {
        background-color: #730000;
        padding: 14px 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }

    .navbar a {
        color: #FF9B9B;
        text-decoration: none;
        margin: 0 12px;
        font-weight: 500;
    }

    .navbar a:hover {
        color: #FE0000;
    }

    .nav-title {
        font-size: 1.4rem;
        font-weight: bold;
    }

    .container {
        padding: 20px;
        max-width: 1000px;
        margin: auto;
    }

    h2 {
        text-align: center;
        margin-top: 30px;
        color: #AF0000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        background-color: #FF9B9B;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 35, 42, 0.1);
    }

    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #fff;
        text-align: left;
    }

    th {
        background-color: #AF0000;
        color: #fff;
    }

    tr:hover {
        background-color: #FFEDED;
    }

    .btn-approve {
        background-color: #00232A;
        color: #FF9B9B;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.3s;
    }

    .btn-approve:hover {
        background-color: #FE0000;
        color: #fff;
    }

    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar a {
            margin: 8px 0;
        }

        table, thead, tbody, th, td, tr {
            display: block;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 15px;
            background-color: #FF9B9B;
            border-radius: 6px;
            padding: 12px;
        }

        td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            width: 45%;
            padding-left: 15px;
            font-weight: bold;
            text-align: left;
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
        <a href="logout.php" style="color: #FE0000;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
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
                    <td data-label="ID"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td data-label="Business Name"><?php echo htmlspecialchars($row['business_name']); ?></td>
                    <td data-label="Contact Person"><?php echo htmlspecialchars($row['contact_person']); ?></td>
                    <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td data-label="Approved"><?php echo $row['approved'] ? "Yes" : "No"; ?></td>
                    <td data-label="Action">
                        <?php if (!$row['approved']): ?>
                            <a 
                                href="dealers.php?approve_id=<?php echo $row['id']; ?>" 
                                class="btn-approve"
                                onclick="return confirm('Are you sure you want to approve this dealer?');"
                            >Approve</a>
                        <?php else: ?>
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
</div>

</body>
</html>
