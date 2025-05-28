<?php
session_start();
include('../includes/db_connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $car_condition = $_POST['car_condition'];
    $description = $_POST['description'];

    // Handle file upload
    $image_name = '';
    if (!empty($_FILES['car_image']['name'])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["car_image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file);
    }

    // Insert into cars table
    $stmt = $conn->prepare("INSERT INTO cars (owner_id, make, model, year, car_condition, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $owner_id, $make, $model, $year, $car_condition, $description, $image_name);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Your Car | SwapRide Kenya</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="submit_car.php" class="active">Submit Car</a></li>
        <li><a href="my_car.php">My Car</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- Submit Car Form -->
<section class="section">
  <div class="container">
    <h2>Submit Your Car for Trade</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
  <p class="success">Car submitted successfully!</p>
<?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="form">
      <label for="make">Make *</label>
      <input type="text" id="make" name="make" required>

      <label for="model">Model *</label>
      <input type="text" id="model" name="model" required>

      <label for="year">Year *</label>
      <input type="number" id="year" name="year" min="1950" max="<?= date('Y') ?>" required>

      <label for="car_condition">Condition *</label>
      <select name="car_condition" id="car_condition" required>
        <option value="">-- Select Condition --</option>
        <option value="new">New</option>
        <option value="used">Used</option>
        <option value="fair">Fair</option>
      </select>

      <label for="description">Description</label>
      <textarea name="description" id="description" rows="4"></textarea>

      <label for="image">Upload Car Image *</label>
      <input type="file" name="image" id="image" accept="image/*" required>

      <button type="submit" class="btn">Submit Car</button>
    </form>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
