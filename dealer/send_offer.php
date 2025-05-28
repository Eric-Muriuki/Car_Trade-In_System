<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Validate car ID from query parameter
if (!isset($_GET['car_id'])) {
    echo "Invalid request. No car specified.";
    exit();
}

$car_id = intval($_GET['car_id']);

// Get car and owner info
$stmt = $conn->prepare("
    SELECT c.*, u.id as user_id, u.full_name, u.email, c.dealer_id as owner_dealer_id
    FROM cars c 
    JOIN users u ON c.owner_id = u.id 
    WHERE c.id = ?
");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    echo "Car not found.";
    exit();
}

// Prevent dealer offering on own car (if dealer_id is owner_dealer_id)
if ($dealer_id == $car['owner_dealer_id']) {
    echo "You cannot send an offer on your own car.";
    exit();
}

$success = $error = "";
$cash_offer_value = "";
$message_value = "";

// Handle offer form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cash_offer_value = $_POST['cash_offer'];
    $message_value = trim($_POST['message']);

    // Validate cash offer (positive number)
    if (!is_numeric($cash_offer_value) || floatval($cash_offer_value) <= 0) {
        $error = "Please enter a valid positive cash offer.";
    } else {
        $cash_offer = floatval($cash_offer_value);

        $insert = $conn->prepare("
            INSERT INTO trade_offers (dealer_id, user_id, car_id, cash_offer, message, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
        ");
        $insert->bind_param("iiids", $dealer_id, $car['user_id'], $car_id, $cash_offer, $message_value);

        if ($insert->execute()) {
            $success = "Trade offer sent successfully!";
            // Clear form values after success
            $cash_offer_value = "";
            $message_value = "";
        } else {
            $error = "Error sending offer. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Offer - <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      width: 80%;
      margin: 30px auto;
    }

    h2 {
      margin-bottom: 10px;
    }

    .car-info {
      background: #f0f0f0;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    form {
      background: #fff;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
    }

    input[type="number"],
    textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .btn {
      margin-top: 15px;
      padding: 10px 18px;
      background: #27ae60;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }

    .success {
      background: #d4edda;
      padding: 10px;
      margin-bottom: 15px;
      border-left: 5px solid #28a745;
    }

    .error {
      background: #f8d7da;
      padding: 10px;
      margin-bottom: 15px;
      border-left: 5px solid #dc3545;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Send Trade Offer for: <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)</h2>

  <div class="car-info">
    <p><strong>Owner:</strong> <?= htmlspecialchars($car['full_name']) ?> (<?= $car['email'] ?>)</p>
    <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($car['description'])) ?></p>
  </div>

  <?php if (!empty($success)): ?>
    <div class="success"><?= $success ?></div>
  <?php elseif (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>

 <form method="POST">
  <label for="cash_offer">Cash Offer (KES):</label>
  <input type="number" step="0.01" name="cash_offer" id="cash_offer" required
         value="<?= htmlspecialchars($cash_offer_value) ?>">

  <label for="message">Message to User:</label>
  <textarea name="message" id="message" rows="5" placeholder="Explain your offer..."><?= htmlspecialchars($message_value) ?></textarea>

  <button type="submit" class="btn">Send Offer</button>
</form>

</div>
</body>
</html>
