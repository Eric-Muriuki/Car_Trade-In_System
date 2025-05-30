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
        users.full_name, users.email
        FROM cars
        JOIN users ON cars.owner_id = users.id
        ORDER BY cars.created_at DESC";  

$result = $conn->query($sql); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Car Listings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFFFA;
            color: #00232A;
        }

        .navbar {
            background-color: #AF0000;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            color: #fff;
        }

        .nav-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links a {
            color: #FFFFFA;
            text-decoration: none;
            margin: 10px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #FF9B9B;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            color: #730000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        thead {
            background-color: #730000;
            color: #FFFFFA;
        }

        tbody tr:nth-child(even) {
            background-color: #FF9B9B;
        }

        tbody tr:nth-child(odd) {
            background-color: #FFFFFA;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .status-pending {
            color: #856404;
            font-weight: bold;
        }

        a.action-link {
            margin-right: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        a.action-link.approve {
            color: green;
        }

        a.action-link.reject {
            color: red;
        }

        a.action-link.delete {
            color: #FE0000;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                width: 100%;
                text-align: center;
            }

            .nav-links a {
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
                border: 1px solid #ccc;
                padding: 10px;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
            }

            td::before {
                position: absolute;
                top: 12px;
                left: 12px;
                width: 45%;
                padding-right: 10px;
                font-weight: bold;
                white-space: nowrap;
            }

            td:nth-child(1)::before { content: "ID"; }
            td:nth-child(2)::before { content: "Make"; }
            td:nth-child(3)::before { content: "Model"; }
            td:nth-child(4)::before { content: "Year"; }
            td:nth-child(5)::before { content: "Price"; }
            td:nth-child(6)::before { content: "Owner"; }
            td:nth-child(7)::before { content: "Email"; }
            td:nth-child(8)::before { content: "Status"; }
            td:nth-child(9)::before { content: "Listed On"; }
            td:nth-child(10)::before { content: "Actions"; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-title"><i class="fas fa-shield-alt"></i> Admin Panel</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="dealers.php">Dealers</a>
        <a href="cars.php">Listings</a>
        <a href="trades.php">Trade Logs</a>
        <a href="logout.php" style="color: #FF9B9B;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>Manage Car Listings</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price (Ksh)</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Listed On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($car = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($car['id']) ?></td>
                        <td><?= htmlspecialchars($car['make']) ?></td>
                        <td><?= htmlspecialchars($car['model']) ?></td>
                        <td><?= htmlspecialchars($car['year']) ?></td>
                        <td><?= number_format($car['price']) ?></td>
                        <td><?= htmlspecialchars($car['full_name']) ?></td>
                        <td><?= htmlspecialchars($car['email']) ?></td>
                        <td class="<?= 
                            $car['is_approved'] == 1 ? 'status-approved' : 
                            ($car['is_approved'] == -1 ? 'status-rejected' : 'status-pending') ?>">
                            <?php 
                                echo $car['is_approved'] == 1 ? "Approved" : 
                                     ($car['is_approved'] == -1 ? "Rejected" : "Pending"); 
                            ?>
                        </td>
                        <td><?= date('d M Y', strtotime($car['created_at'])) ?></td>
                        <td>
                            <?php if ($car['is_approved'] == 0): ?>
                                <a href="cars.php?approve_id=<?= $car['id'] ?>" class="action-link approve" onclick="return confirm('Approve this listing?');">Approve</a>
                                <a href="cars.php?reject_id=<?= $car['id'] ?>" class="action-link reject" onclick="return confirm('Reject this listing?');">Reject</a>
                            <?php endif; ?>
                            <a href="cars.php?delete_id=<?= $car['id'] ?>" class="action-link delete" onclick="return confirm('Delete this listing? This action cannot be undone.');">Delete</a>
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
</body>
</html>
