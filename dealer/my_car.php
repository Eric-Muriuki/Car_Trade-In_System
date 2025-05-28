<?php
session_start();
require_once '../includes/db-connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$message = "";

// Handle car deletion
if (isset($_GET['delete'])) {
    $car_id = intval($_GET['delete']);

    // Delete photo from server
    $photo_query = $conn->prepare("SELECT photo FROM cars WHERE id = ? AND dealer_id = ?");
    $photo_query->bind_param("ii", $car_id, $dealer_id);
    $photo_query->execute();
    $photo_result = $photo_query->get_result()->fetch_assoc();
    if ($photo_result && file_exists("../uploads/cars/" . $photo_result['photo'])) {
        unlink("../uploads/cars/" . $photo_result['photo']);
    }

    // Delete car from DB
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND dealer_id = ?");
    $stmt->bind_param("ii", $car_id, $dealer_id);
    if ($stmt->execute()) {
        $message = "Car deleted successfully.";
    } else {
        $message = "Error deleting car.";
    }
}

// Fetch dealer cars
$stmt = $conn->prepare("SELECT * FROM cars WHERE dealer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Inventory - Dealer</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      width: 90%;
      margin: 30px auto;
    }

    h2 {
      margin-bottom: 20px;
    }

    .message {
      padding: 10px;
      background: #d4edda;
      color: #155724;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background: #f2f2f2;
    }

    img {
      width: 100px;
      height: auto;
      border-radius: 5px;
    }

    .btn {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      color: #fff;
      cursor: pointer;
      text-decoration: none;
    }

    .edit-btn {
      background: #3498db;
    }

    .delete-btn {
      background: #e74c3c;
    }

    .add-btn {
      background: #2ecc71;
      float: right;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>My Inventory</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <a href="add_car.php" class="btn add-btn">+ Add New Car</a>

    <table>
      <thead>
        <tr>
          <th>Photo</th>
          <th>Make & Model</th>
          <th>Year</th>
          <th>Mileage</th>
          <th>Price (KES)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($car = $result->fetch_assoc()): ?>
            <tr>
              <td><img src="../uploads/cars/<?= htmlspecialchars($car['photo']) ?>" alt="Car"></td>
              <td><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></td>
              <td><?= htmlspecialchars($car['year']) ?></td>
              <td><?= number_format($car['mileage']) ?> km</td>
              <td>KES <?= number_format($car['price'], 2) ?></td>
              <td>
                <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $car['id'] ?>" onclick="return confirm('Are you sure you want to delete this car?');" class="btn delete-btn">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">You have no cars listed yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
