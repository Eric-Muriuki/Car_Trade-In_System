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

// Get car info and owner info
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

// Find or create a trade record for this car, owner, and dealer
// This assumes one trade per car + dealer pair
$trade_stmt = $conn->prepare("SELECT id FROM trades WHERE user_car_id = ? AND user_id = ? AND dealer_id = ?");
$trade_stmt->bind_param("iii", $car_id, $car['user_id'], $dealer_id);
$trade_stmt->execute();
$trade_result = $trade_stmt->get_result();

if ($trade = $trade_result->fetch_assoc()) {
    $trade_id = $trade['id'];
} else {
    // No existing trade, create one
    $insert_trade = $conn->prepare("INSERT INTO trades (user_car_id, user_id, dealer_id) VALUES (?, ?, ?)");
    $insert_trade->bind_param("iii", $car_id, $car['user_id'], $dealer_id);
    if ($insert_trade->execute()) {
        $trade_id = $insert_trade->insert_id;
    } else {
        die("Failed to create trade record: " . $conn->error);
    }
}

// Variables for form repopulation and messages
$success = $error = "";
$cash_offer_value = "";
$message_value = "";

// Handle offer form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cash_offer_value = $_POST['cash_offer'] ?? "";
    $message_value = trim($_POST['message'] ?? "");

    // Validate cash offer (positive number)
    if (!is_numeric($cash_offer_value) || floatval($cash_offer_value) <= 0) {
        $error = "Please enter a valid positive cash offer.";
    } else {
        $cash_offer = floatval($cash_offer_value);

        // Insert offer into 'offers' table
        $insert_offer = $conn->prepare("
            INSERT INTO offers (trade_id, dealer_id, offer_price, message, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $insert_offer->bind_param("iids", $trade_id, $dealer_id, $cash_offer, $message_value);

        if ($insert_offer->execute()) {
            $success = "Trade offer sent successfully!";
            $cash_offer_value = "";
            $message_value = "";
        } else {
            $error = "Error sending offer: " . $insert_offer->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Send Offer - <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></title>
<style>
/* Add your blended color styles here */
:root {
  --red-bright: #FE0000;
  --red-dark: #AF0000;
  --cream: #FFFFFA;
  --pink-light: #FF9B9B;
  --dark-blue: #00232A;
  --maroon: #730000;

  --bg-color: var(--cream);
  --form-bg: white;
  --input-border: var(--maroon);
  --text-color: var(--dark-blue);
  --btn-bg: var(--red-bright);
  --btn-hover-bg: var(--red-dark);
  --success-bg: #d4edda;
  --success-text: #155724;
  --error-bg: #f8d7da;
  --error-text: #721c24;
  --label-color: var(--maroon);
}

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
  padding: 20px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.container {
  max-width: 600px;
  margin: 30px auto;
  background: var(--form-bg);
  padding: 30px 25px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(115, 0, 0, 0.2);
}

h2 {
  margin-bottom: 15px;
  color: var(--maroon);
  font-weight: 700;
  font-size: 1.6rem;
  text-align: center;
}

.car-info {
  background: var(--pink-light);
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  color: var(--maroon);
  box-shadow: inset 0 0 8px rgba(115, 0, 0, 0.15);
  font-size: 1rem;
  line-height: 1.5;
}

.car-info strong {
  color: var(--red-dark);
}

form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

label {
  font-weight: 600;
  font-size: 1rem;
  color: var(--label-color);
}

input[type="number"],
textarea {
  font-family: inherit;
  font-size: 1rem;
  padding: 12px 14px;
  border-radius: 8px;
  border: 2px solid var(--input-border);
  background: var(--cream);
  color: var(--dark-blue);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  resize: vertical;
}

input[type="number"]:focus,
textarea:focus {
  border-color: var(--red-bright);
  box-shadow: 0 0 10px var(--red-bright);
  outline: none;
  background: #fff;
}

button.btn {
  background-color: var(--btn-bg);
  color: var(--cream);
  border: none;
  font-weight: 700;
  font-size: 1.15rem;
  padding: 14px 0;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(254, 0, 0, 0.7);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
}

button.btn:hover,
button.btn:focus {
  background-color: var(--btn-hover-bg);
  box-shadow: 0 8px 22px rgba(175, 0, 0, 0.85);
  outline: none;
}

.success,
.error {
  padding: 14px 18px;
  margin-bottom: 20px;
  border-radius: 8px;
  font-weight: 600;
  text-align: center;
  box-shadow: inset 0 0 12px rgba(115, 0, 0, 0.2);
}

.success {
  background-color: var(--success-bg);
  color: var(--success-text);
  border-left: 6px solid #28a745;
}

.error {
  background-color: var(--error-bg);
  color: var(--error-text);
  border-left: 6px solid #dc3545;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .container {
    width: 90%;
    padding: 25px 20px;
  }

  h2 {
    font-size: 1.4rem;
  }

  input[type="number"],
  textarea,
  button.btn {
    font-size: 1rem;
    padding: 12px 14px;
  }
}

@media (max-width: 400px) {
  input[type="number"],
  textarea {
    padding: 10px 12px;
  }
}
</style>
</head>
<body>
<div class="container" role="main">
  <h2>Send Trade Offer for: <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)</h2>

  <div class="car-info" aria-label="Car and owner details">
    <p><strong>Owner:</strong> <?= htmlspecialchars($car['full_name']) ?> (<?= htmlspecialchars($car['email']) ?>)</p>
    <p><strong>Price Asking:</strong> Ksh <?= number_format($car['price'], 2) ?></p>
    <p><strong>Condition:</strong> <?= htmlspecialchars($car['car_condition']) ?></p>
  </div>

  <?php if ($success): ?>
    <div class="success" role="alert"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" novalidate aria-describedby="form-instructions">
    <p id="form-instructions">Please enter your cash offer amount and an optional message.</p>
    <label for="cash_offer">Cash Offer (Ksh):</label>
    <input
      type="number"
      id="cash_offer"
      name="cash_offer"
      min="1"
      step="0.01"
      required
      value="<?= htmlspecialchars($cash_offer_value) ?>"
      aria-required="true"
      aria-describedby="cash_offer_help"
    />
    <small id="cash_offer_help">Enter your proposed cash offer in Kenyan shillings (Ksh).</small>

    <label for="message">Message (optional):</label>
    <textarea
      id="message"
      name="message"
      rows="5"
      maxlength="500"
      aria-describedby="message_help"
    ><?= htmlspecialchars($message_value) ?></textarea>
    <small id="message_help">Add any message or comments for the owner.</small>

    <button type="submit" class="btn" aria-label="Submit offer">Send Offer</button>
  </form>
</div>
</body>
</html>
