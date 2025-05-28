<?php
session_start();
require_once '../includes/db-connect.php';

// Check dealer login
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch current dealer info
$sql = "SELECT * FROM dealers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$dealer = $result->fetch_assoc();

// Handle form submission
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = trim($_POST['business_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $kra_pin = trim($_POST['kra_pin']);
    $address = trim($_POST['address']);

    if ($business_name && $email && $phone && $kra_pin && $address) {
        $update_sql = "UPDATE dealers SET business_name=?, email=?, phone=?, kra_pin=?, address=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $business_name, $email, $phone, $kra_pin, $address, $dealer_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh dealer info
            $dealer = ['business_name' => $business_name, 'email' => $email, 'phone' => $phone, 'kra_pin' => $kra_pin, 'address' => $address];
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dealer Profile</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: #f9f9f9;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      background-color: #333;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .message {
      padding: 10px;
      margin-bottom: 20px;
    }
    .success {
      background: #d4edda;
      color: #155724;
    }
    .error {
      background: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Dealer Profile</h2>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="business_name">Business Name</label>
      <input type="text" name="business_name" id="business_name" required value="<?= htmlspecialchars($dealer['business_name']) ?>">

      <label for="email">Email</label>
      <input type="email" name="email" id="email" required value="<?= htmlspecialchars($dealer['email']) ?>">

      <label for="phone">Phone Number</label>
      <input type="text" name="phone" id="phone" required value="<?= htmlspecialchars($dealer['phone']) ?>">

      <label for="kra_pin">KRA PIN</label>
      <input type="text" name="kra_pin" id="kra_pin" required value="<?= htmlspecialchars($dealer['kra_pin']) ?>">

      <label for="address">Business Address</label>
      <textarea name="address" id="address" required><?= htmlspecialchars($dealer['address']) ?></textarea>

      <button type="submit">Update Profile</button>
    </form>
  </div>
</body>
</html>
