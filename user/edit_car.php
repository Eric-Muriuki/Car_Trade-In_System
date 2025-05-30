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
  <meta charset="UTF-8" />
  <title>Edit Car | SwapRide Kenya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --red-soft: #FF9B9B;
      --red-deep: #730000;
      --whiteish: #FFFFFA;
      --blue-dark: #00232A;
      --shadow-light: rgba(254, 0, 0, 0.15);
      --shadow-medium: rgba(254, 0, 0, 0.3);
      --shadow-dark: rgba(175, 0, 0, 0.5);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--blue-dark), var(--red-deep));
      color: var(--whiteish);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .container {
      max-width: 900px;
      margin: auto;
      padding: 20px 25px;
    }

    /* Header */
    header.header {
      background: var(--whiteish);
      color: var(--blue-dark);
      box-shadow: 0 4px 15px var(--shadow-light);
      padding: 15px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--red-deep);
      text-shadow: 1px 1px 2px var(--red-soft);
      user-select: none;
    }

    nav ul.nav-links {
      list-style: none;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    nav ul.nav-links li {
      display: inline;
    }

    nav ul.nav-links li a {
      text-decoration: none;
      font-weight: 600;
      color: var(--red-dark);
      padding: 8px 15px;
      border-radius: 8px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background: var(--red-primary);
      color: var(--whiteish);
      box-shadow: 0 4px 12px var(--shadow-medium);
    }

    nav ul.nav-links li a.btn {
      background: var(--red-primary);
      color: var(--whiteish);
      border: none;
      cursor: pointer;
    }

    /* Section */
    section.section {
      background: var(--whiteish);
      border-radius: 14px;
      padding: 30px 40px;
      margin: 30px auto;
      box-shadow:
        0 8px 24px var(--shadow-medium),
        inset 0 0 30px var(--red-soft);
      color: var(--blue-dark);
      max-width: 900px;
    }

    section.section h2 {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 25px;
      color: var(--red-deep);
      text-shadow: 1px 1px 3px var(--red-soft);
      text-align: center;
    }

    .success, .error {
      font-weight: 600;
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .success {
      background-color: var(--red-soft);
      color: var(--red-deep);
      box-shadow: 0 0 10px var(--red-primary);
    }

    .error {
      background-color: #ffcccc;
      color: #730000;
      box-shadow: 0 0 10px var(--red-deep);
    }

    form.form-box {
      display: flex;
      flex-direction: column;
      gap: 18px;
      max-width: 700px;
      margin: auto;
    }

    form.form-box label {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--red-deep);
    }

    form.form-box input[type="text"],
    form.form-box input[type="number"],
    form.form-box textarea,
    form.form-box input[type="file"] {
      padding: 10px;
      border-radius: 8px;
      border: 1.5px solid var(--red-dark);
      font-size: 1rem;
      background: var(--whiteish);
      color: var(--blue-dark);
      transition: border-color 0.3s ease;
      resize: vertical;
      width: 100%;
    }

    form.form-box input[type="text"]:focus,
    form.form-box input[type="number"]:focus,
    form.form-box textarea:focus,
    form.form-box input[type="file"]:focus {
      outline: none;
      border-color: var(--red-primary);
      box-shadow: 0 0 8px var(--red-primary);
    }

    form.form-box textarea {
      min-height: 100px;
    }

    img.car-image {
      display: block;
      max-width: 200px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 4px 12px var(--shadow-dark);
      margin: 10px 0 20px 0;
      user-select: none;
    }

    form.form-box button.btn {
      background: var(--red-primary);
      color: var(--whiteish);
      border: none;
      padding: 14px 0;
      font-weight: 700;
      font-size: 1.2rem;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      width: 100%;
    }

    form.form-box button.btn:hover,
    form.form-box button.btn:focus {
      background: var(--red-dark);
      box-shadow: 0 0 15px var(--shadow-dark);
      outline: none;
    }

    /* Footer */
    footer.footer {
      background: var(--whiteish);
      color: var(--blue-dark);
      text-align: center;
      padding: 15px 10px;
      box-shadow: 0 -4px 15px var(--shadow-light);
      user-select: none;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
      section.section {
        padding: 25px 20px;
        margin: 20px 15px;
      }

      form.form-box {
        max-width: 100%;
      }

      form.form-box input[type="text"],
      form.form-box input[type="number"],
      form.form-box textarea,
      form.form-box input[type="file"] {
        font-size: 0.95rem;
      }

      form.form-box button.btn {
        font-size: 1rem;
      }

      nav ul.nav-links {
        gap: 12px;
      }
    }

    @media (max-width: 480px) {
      header.header .container {
        flex-direction: column;
        gap: 12px;
      }
      nav ul.nav-links {
        flex-direction: column;
        gap: 10px;
        align-items: center;
      }
      section.section {
        padding: 20px 15px;
        margin: 15px 10px;
      }
      form.form-box label {
        font-size: 1rem;
      }
      form.form-box button.btn {
        font-size: 0.95rem;
        padding: 12px 0;
      }
      img.car-image {
        max-width: 100%;
        margin: 15px 0;
      }
    }
  </style>
</head>
<body>
<header class="header">
  <div class="container">
    <h1 class="logo">SwapRide Kenya</h1>
    <nav>
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="my_car.php" class="active">My Car</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="section" role="main" aria-labelledby="editCarHeading">
  <div class="container">
    <h2 id="editCarHeading">Edit Car Listing</h2>

    <?php if ($success): ?>
      <p class="success" role="alert">Car details updated successfully!</p>
    <?php elseif ($error): ?>
      <p class="error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="form-box" novalidate>
      <label for="make">Make:</label>
      <input type="text" id="make" name="make" value="<?= htmlspecialchars($car['make']) ?>" required autocomplete="off" />

      <label for="model">Model:</label>
      <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>" required autocomplete="off" />

      <label for="year">Year:</label>
      <input type="number" id="year" name="year" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($car['year']) ?>" required />

      <label for="mileage">Mileage (km):</label>
      <input type="number" id="mileage" name="mileage" min="0" value="<?= htmlspecialchars($car['mileage']) ?>" required />

      <label for="price">Price (KES):</label>
      <input type="number" id="price" name="price" min="0" step="0.01" value="<?= htmlspecialchars($car['price']) ?>" required />

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($car['description']) ?></textarea>

      <label>Current Image:</label><br>
      <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" alt="Current car image" class="car-image" loading="lazy" /><br>

      <label for="image">Change Image (optional):</label>
      <input type="file" id="image" name="image" accept="image/*" />

      <button type="submit" class="btn" aria-label="Update Car Details">Update Car</button>
    </form>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

<script>
  // Client-side validation enhancement (optional)
  document.querySelector('form.form-box').addEventListener('submit', function(e) {
    const year = parseInt(document.getElementById('year').value, 10);
    const currentYear = new Date().getFullYear();
    if (year < 1900 || year > currentYear) {
      alert(`Please enter a valid year between 1900 and ${currentYear}.`);
      e.preventDefault();
      return false;
    }

    const mileage = parseInt(document.getElementById('mileage').value, 10);
    if (mileage < 0) {
      alert("Mileage cannot be negative.");
      e.preventDefault();
      return false;
    }

    const price = parseFloat(document.getElementById('price').value);
    if (price < 0) {
      alert("Price cannot be negative.");
      e.preventDefault();
      return false;
    }
  });
</script>
</body>
</html>
