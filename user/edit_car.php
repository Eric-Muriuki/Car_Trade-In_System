<?php
session_start();
include('../includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = false;
$error = "";

// Fetch existing car data
$query = "SELECT * FROM cars WHERE id = ? AND owner_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $car_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Car not found or access denied.");
}

$car = $result->fetch_assoc();

// Update logic on form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $mileage = intval($_POST['mileage']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    // Image logic
    $image = $car['image'];
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $target_dir = "../uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $update = "UPDATE cars SET make=?, model=?, year=?, mileage=?, price=?, description=?, image=? WHERE id=? AND owner_id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssiidssii", $make, $model, $year, $mileage, $price, $description, $image, $car_id, $owner_id);

    if ($stmt->execute()) {
        $success = true;
        // Refresh car data
        $car = ['make' => $make, 'model' => $model, 'year' => $year, 'mileage' => $mileage, 'price' => $price, 'description' => $description, 'image' => $image];
    } else {
        $error = "Failed to update car details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Car | SwapRide Kenya</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="my_car.php">My Car</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="section">
  <div class="container">
    <h2>Edit Car Listing</h2>

    <?php if ($success): ?>
      <p class="success">Car details updated successfully!</p>
    <?php elseif ($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="form-box">
      <label>Make:</label>
      <input type="text" name="make" value="<?= htmlspecialchars($car['make']) ?>" required>

      <label>Model:</label>
      <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>

      <label>Year:</label>
      <input type="number" name="year" value="<?= htmlspecialchars($car['year']) ?>" required>

      <label>Mileage (km):</label>
      <input type="number" name="mileage" value="<?= htmlspecialchars($car['mileage']) ?>" required>

      <label>Price (KES):</label>
      <input type="number" name="price" value="<?= htmlspecialchars($car['price']) ?>" step="0.01" required>

      <label>Description:</label>
      <textarea name="description" rows="4" required><?= htmlspecialchars($car['description']) ?></textarea>

      <label>Current Image:</label><br>
      <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" alt="Car Image" width="200"><br><br>

      <label>Change Image (optional):</label>
      <input type="file" name="image" accept="image/*">

      <button type="submit" class="btn">Update Car</button>
    </form>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
