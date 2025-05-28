<?php
session_start();
require_once '../includes/db_connect.php';

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

    // Fetch image filename
    $photo_query = $conn->prepare("SELECT image FROM cars WHERE id = ? AND dealer_id = ?");
    $photo_query->bind_param("ii", $car_id, $dealer_id);
    $photo_query->execute();
    $photo_result = $photo_query->get_result()->fetch_assoc();
    if ($photo_result && file_exists("../uploads/cars/" . $photo_result['image'])) {
        unlink("../uploads/cars/" . $photo_result['image']);
    }

    // Delete car
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND dealer_id = ?");
    $stmt->bind_param("ii", $car_id, $dealer_id);
    if ($stmt->execute()) {
        $message = "Car deleted successfully.";
    } else {
        $message = "Error deleting car.";
    }
}

// Fetch cars listed by this dealer
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
</head>
<body>
  

  <div class="container">
    <h2>My Inventory</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <a href="add_car.php" class="btn add-btn">+ Add New Car</a>

    <table class="table">
      <thead>
        <tr>
          <th>Photo</th>
          <th>Make & Model</th>
          <th>Year</th>
          <th>Mileage</th>
          <th>Price (KES)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($car = $result->fetch_assoc()): ?>
            <tr>
              <td>
                <?php if (!empty($car['image'])): ?>
                  <img src="../uploads/cars/<?= htmlspecialchars($car['image']) ?>" alt="Car">
                <?php else: ?>
                  <span>No image</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></td>
              <td><?= htmlspecialchars($car['year']) ?></td>
              <td><?= number_format($car['mileage']) ?> km</td>
              <td>KES <?= number_format($car['price'], 2) ?></td>
              <td><?= ucfirst($car['status']) ?></td>
              <td>
                <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn edit-btn">Edit</a>
                <a href="?delete=<?= $car['id'] ?>" onclick="return confirm('Are you sure you want to delete this car?');" class="btn delete-btn">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">You have no cars listed yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
</body>
</html>
