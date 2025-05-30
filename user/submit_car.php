<?php
session_start();
include('../includes/db_connect.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $year = intval($_POST['year']);
    $car_condition = $_POST['car_condition'];
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($make)) $errors[] = "Make is required.";
    if (empty($model)) $errors[] = "Model is required.";
    if ($year < 1950 || $year > intval(date('Y'))) $errors[] = "Year must be between 1950 and " . date('Y') . ".";
    if (!in_array($car_condition, ['new', 'used', 'fair'])) $errors[] = "Please select a valid car condition.";

    // Handle file upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Invalid image type. Allowed types: JPG, PNG, GIF, WEBP.";
        } else {
            $target_dir = "../uploads/";
            // Generate unique file name to prevent overwriting
            $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $image_name = uniqid('car_', true) . "." . $ext;
            $target_file = $target_dir . $image_name;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors[] = "Failed to upload image.";
            }
        }
    } else {
        $errors[] = "Car image is required.";
    }

    if (empty($errors)) {
        // Insert into cars table
        $stmt = $conn->prepare("INSERT INTO cars (owner_id, make, model, year, car_condition, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississs", $owner_id, $make, $model, $year, $car_condition, $description, $image_name);

        if ($stmt->execute()) {
            $success = true;
            // Clear POST data to reset form
            $_POST = [];
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Submit Your Car | SwapRide Kenya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --red-primary: #FE0000;
      --red-medium: #AF0000;
      --red-light: #FF9B9B;
      --red-dark: #730000;
      --blue-dark: #00232A;
      --off-white: #FFFFFA;
    }
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--red-light), var(--off-white));
      color: var(--blue-dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .container {
      width: 90%;
      max-width: 700px;
      margin: 0 auto;
      padding: 20px 0;
    }
    header.header {
      background: var(--red-primary);
      padding: 15px 0;
      box-shadow: 0 4px 12px rgba(254,0,0,0.6);
      user-select: none;
    }
    header .logo {
      color: var(--off-white);
      margin: 0;
      text-align: center;
      font-size: 1.8rem;
      font-weight: 900;
      letter-spacing: 0.07em;
      font-family: 'Segoe UI Black', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    nav ul.nav-links {
      list-style: none;
      padding: 0;
      margin: 10px 0 0;
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    nav ul.nav-links li a {
      color: var(--off-white);
      text-decoration: none;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 25px;
      background: var(--red-medium);
      box-shadow: 0 3px 8px rgba(175,0,0,0.6);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background: var(--red-dark);
      box-shadow: 0 5px 15px var(--red-dark);
    }

    section.section {
      flex-grow: 1;
      padding: 25px 0;
    }
    h2 {
      margin-bottom: 25px;
      color: var(--red-primary);
      text-align: center;
      font-weight: 800;
      letter-spacing: 0.05em;
    }

    form.form {
      background: var(--off-white);
      padding: 30px 25px;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(254,0,0,0.2);
      max-width: 600px;
      margin: 0 auto;
      user-select: text;
    }

    label {
      display: block;
      margin: 15px 0 6px;
      font-weight: 700;
      color: var(--red-dark);
      font-size: 1rem;
    }
    input[type="text"], 
    input[type="number"], 
    select, 
    textarea, 
    input[type="file"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid var(--red-medium);
      border-radius: 12px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
      font-weight: 600;
      color: var(--blue-dark);
      background: var(--off-white);
      resize: vertical;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus,
    textarea:focus,
    input[type="file"]:focus {
      border-color: var(--red-primary);
      outline: none;
      box-shadow: 0 0 8px var(--red-light);
    }

    button.btn {
      margin-top: 30px;
      width: 100%;
      padding: 14px 0;
      background: var(--red-primary);
      color: var(--off-white);
      font-weight: 900;
      font-size: 1.1rem;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      box-shadow: 0 5px 20px rgba(254,0,0,0.5);
      transition: background-color 0.4s ease, box-shadow 0.4s ease;
      user-select: none;
    }
    button.btn:hover,
    button.btn:focus {
      background: var(--red-dark);
      box-shadow: 0 8px 30px var(--red-dark);
      outline: none;
    }

    .alert {
      max-width: 600px;
      margin: 0 auto 20px;
      padding: 14px 18px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      line-height: 1.3;
    }
    .alert-error {
      background: #FDECEA;
      color: #720000;
      border: 1.5px solid #F99595;
      user-select: text;
    }
    .success {
      max-width: 600px;
      margin: 0 auto 20px;
      padding: 14px 18px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      line-height: 1.3;
      background: #DFF2E1;
      color: #065F46;
      border: 1.5px solid #A5D6A7;
      text-align: center;
      user-select: text;
    }

    footer.footer {
      background: var(--red-primary);
      padding: 15px 0;
      text-align: center;
      color: var(--off-white);
      user-select: none;
      margin-top: auto;
    }

    @media (max-width: 600px) {
      nav ul.nav-links {
        flex-direction: column;
        gap: 10px;
      }
      .container {
        width: 95%;
        padding: 10px 0;
      }
      form.form {
        padding: 20px 15px;
      }
      button.btn {
        font-size: 1rem;
      }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('form.form');

      form.addEventListener('submit', e => {
        const errors = [];
        const make = form.make.value.trim();
        const model = form.model.value.trim();
        const year = parseInt(form.year.value);
        const carCondition = form.car_condition.value;
        const fileInput = form.image;
        const currentYear = new Date().getFullYear();

        if (!make) errors.push("Make is required.");
        if (!model) errors.push("Model is required.");
        if (!year || year < 1950 || year > currentYear) errors.push(`Year must be between 1950 and ${currentYear}.`);
        if (!['new', 'used', 'fair'].includes(carCondition)) errors.push("Please select a valid car condition.");
        if (!fileInput.files || fileInput.files.length === 0) errors.push("Car image is required.");

        if (errors.length > 0) {
          e.preventDefault();
          alert(errors.join("\n"));
        }
      });
    });
  </script>
</head>
<body>

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

<section class="section">
  <div class="container">

    <h2>Submit Your Car for Trade</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error" role="alert">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <p class="success" role="alert">Car submitted successfully!</p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="form" novalidate>
      <label for="make">Make *</label>
      <input type="text" id="make" name="make" required value="<?= isset($_POST['make']) ? htmlspecialchars($_POST['make']) : '' ?>">

      <label for="model">Model *</label>
      <input type="text" id="model" name="model" required value="<?= isset($_POST['model']) ? htmlspecialchars($_POST['model']) : '' ?>">

      <label for="year">Year *</label>
      <input type="number" id="year" name="year" min="1950" max="<?= date('Y') ?>" required value="<?= isset($_POST['year']) ? intval($_POST['year']) : '' ?>">

      <label for="car_condition">Condition *</label>
      <select name="car_condition" id="car_condition" required>
        <option value="">-- Select Condition --</option>
        <option value="new" <?= (isset($_POST['car_condition']) && $_POST['car_condition'] === 'new') ? 'selected' : '' ?>>New</option>
        <option value="used" <?= (isset($_POST['car_condition']) && $_POST['car_condition'] === 'used') ? 'selected' : '' ?>>Used</option>
        <option value="fair" <?= (isset($_POST['car_condition']) && $_POST['car_condition'] === 'fair') ? 'selected' : '' ?>>Fair</option>
      </select>

      <label for="description">Description</label>
      <textarea name="description" id="description" rows="4"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>

      <label for="image">Upload Car Image *</label>
      <input type="file" name="image" id="image" accept="image/*" required>

      <button type="submit" class="btn">Submit Car</button>
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
