<?php
session_start();
require_once '../includes/db-connect.php';

// Check dealer login
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch trade offers sent by this dealer
$sql = "
    SELECT t.*, 
           c.make, c.model, c.year, 
           u.full_name AS user_name
    FROM trade_offers t
    JOIN cars c ON t.car_id = c.id
    JOIN users u ON t.user_id = u.id
    WHERE t.dealer_id = ?
    ORDER BY t.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Trade Offers</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      width: 90%;
      margin: 30px auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #333;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 4px;
    }

    .Pending {
      color: #f39c12;
    }

    .Accepted {
      color: #27ae60;
    }

    .Declined {
      color: #c0392b;
    }

    h2 {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Manage Trade Offers</h2>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Car</th>
          <th>Owner</th>
          <th>Cash Offer (KES)</th>
          <th>Message</th>
          <th>Status</th>
          <th>Sent On</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($offer = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($offer['make'] . ' ' . $offer['model'] . ' (' . $offer['year'] . ')') ?></td>
            <td><?= htmlspecialchars($offer['user_name']) ?></td>
            <td><?= number_format($offer['cash_offer'], 2) ?></td>
            <td><?= nl2br(htmlspecialchars($offer['message'])) ?></td>
            <td><span class="status <?= $offer['status'] ?>"><?= $offer['status'] ?></span></td>
            <td><?= date('d M Y, H:i', strtotime($offer['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No trade offers found.</p>
  <?php endif; ?>
</div>
</body>
</html>
