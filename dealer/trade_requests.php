<?php
session_start();
require_once '../includes/db-connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch trade-in requests (cars listed by users)
$query = "
    SELECT c.*, u.full_name, u.email
    FROM cars c
    JOIN users u ON c.owner_id = u.id
    WHERE c.listed_for_trade = 1
    ORDER BY c.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trade-In Requests - Dealer</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      width: 90%;
      margin: 30px auto;
    }

    h2 {
      margin-bottom: 20px;
    }

    .car-card {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      background: #f9f9f9;
    }

    .car-card img {
      width: 200px;
      height: auto;
      border-radius: 5px;
      margin-right: 20px;
    }

    .car-details {
      flex: 1;
    }

    .car-details h3 {
      margin-top: 0;
      margin-bottom: 10px;
    }

    .car-details p {
      margin: 5px 0;
    }

    .btn {
      padding: 8px 14px;
      background: #2ecc71;
      color: #fff;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      cursor: pointer;
      margin-top: 10px;
    }

    .btn:hover {
      background: #27ae60;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Trade-In Requests</h2>

    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($car = $result->fetch_assoc()): ?>
        <div class="car-card">
          <img src="../uploads/cars/<?= htmlspecialchars($car['photo']) ?>" alt="Car">
          <div class="car-details">
            <h3><?= htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) ?> (<?= $car['year'] ?>)</h3>
            <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
            <p><strong>Owner:</strong> <?= htmlspecialchars($car['full_name']) ?> (<?= $car['email'] ?>)</p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($car['description'])) ?></p>
            <a href="send_offer.php?car_id=<?= $car['id'] ?>" class="btn">Send Trade Offer</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No trade-in requests available at the moment.</p>
    <?php endif; ?>
  </div>
</body>
</html>
