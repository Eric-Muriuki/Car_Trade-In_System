<?php
session_start();
require_once '../includes/db_connect.php';

// Check if dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $mileage = $_POST['mileage'];
    $description = $_POST['description'];
    $dealer_id = $_SESSION['dealer_id'];

    // Handle image upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $photoName = uniqid() . "_" . basename($_FILES['photo']['name']);
        $targetDir = "../uploads/cars/";
        $targetFile = $targetDir . $photoName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                // Insert car into DB
                $stmt = $conn->prepare("INSERT INTO cars (dealer_id, make, model, year, price, mileage, description, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssddss", $dealer_id, $make, $model, $year, $price, $mileage, $description, $photoName);

                if ($stmt->execute()) {
                    $success = "Car added successfully!";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
            } else {
                $error = "Failed to upload photo.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, or WEBP files are allowed.";
        }
    } else {
        $error = "Please upload a photo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Car - Dealer</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
    }

    .container {
      width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    form input, form textarea, form select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background: #3498db;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .message {
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
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
    <h2>Add New Car</h2>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
      <label>Make:</label>
      <input type="text" name="make" required>

      <label>Model:</label>
      <input type="text" name="model" required>

      <label>Year:</label>
      <input type="text" name="year" required>

      <label>Mileage (km):</label>
      <input type="number" name="mileage" required>

      <label>Price (KES):</label>
      <input type="number" step="0.01" name="price" required>

      <label>Description:</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Upload Photo:</label>
      <input type="file" name="photo" accept="image/*" required>

      <button type="submit">Add Car</button>
    </form>
  </div>
</body>
</html>
