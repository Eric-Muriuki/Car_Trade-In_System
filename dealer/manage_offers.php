<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

$sql = "
    SELECT t.*, 
           c.make, c.model, c.year, 
           u.full_name AS user_name
    FROM offers t
    JOIN cars c ON t.id = c.id
    JOIN users u ON t.id = u.id
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
    /* Your CSS here... */
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
        <?php while ($offer = $result->fetch_assoc()): 
            $status_class = strtolower($offer['status']);
        ?>
          <tr>
            <td><?= htmlspecialchars($offer['make'] . ' ' . $offer['model'] . ' (' . $offer['year'] . ')') ?></td>
            <td><?= htmlspecialchars($offer['user_name']) ?></td>
            <td><?= number_format($offer['cash_offer'], 2) ?></td>
            <td><?= nl2br(htmlspecialchars($offer['message'])) ?></td>
            <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($offer['status']) ?></span></td>
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
