<?php
session_start();
require_once '../includes/db_connect.php';

// Check if dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer/login.php");
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
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
        $targetDir = "../images/cars/";
        $targetFile = $targetDir . $imageName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
    $stmt = $conn->prepare("INSERT INTO cars (owner_id, dealer_id, make, model, year, price, mileage, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssddss", $dealer_id, $dealer_id, $make, $model, $year, $price, $mileage, $description, $imageName);

    if ($stmt->execute()) {
        $success = "Car added successfully!";
    } else {
        $error = "Database error: " . $stmt->error;
    }
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, or WEBP files are allowed.";
        }
    } else {
        $error = "Please upload an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>Add New Car - Dealer</title>
  <style>
    /* Color palette */
    :root {
      --red-light: #FF9B9B;
      --red-base: #FE0000;
      --red-dark: #AF0000;
      --red-darker: #730000;
      --cream: #FFFFFA;
      --dark-teal: #00232A;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--cream), var(--red-light));
      color: var(--dark-teal);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }

    main.container {
      background-color: var(--cream);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(254, 0, 0, 0.3);
      max-width: 480px;
      width: 100%;
      padding: 2rem 2.5rem;
      transition: box-shadow 0.3s ease;
    }
    main.container:hover {
      box-shadow: 0 12px 36px rgba(175, 0, 0, 0.5);
    }

    h2 {
      margin-top: 0;
      margin-bottom: 1.5rem;
      color: var(--red-base);
      text-align: center;
      text-shadow: 1px 1px 2px var(--red-darker);
      font-weight: 700;
      font-size: 2rem;
      letter-spacing: 1.2px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    label {
      font-weight: 600;
      font-size: 1rem;
      color: var(--red-dark);
      margin-bottom: 0.3rem;
      user-select: none;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
      padding: 0.65rem 1rem;
      border: 2px solid var(--red-light);
      border-radius: 8px;
      font-size: 1rem;
      color: var(--dark-teal);
      background-color: var(--cream);
      transition: border-color 0.3s ease;
      width: 100%;
      resize: vertical;
      font-family: inherit;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus,
    input[type="file"]:focus {
      outline: none;
      border-color: var(--red-base);
      box-shadow: 0 0 8px var(--red-base);
      background-color: #fff;
    }

    textarea {
      min-height: 100px;
    }

    button[type="submit"] {
      background: linear-gradient(135deg, var(--red-base), var(--red-dark));
      border: none;
      border-radius: 8px;
      color: var(--cream);
      font-weight: 700;
      font-size: 1.1rem;
      padding: 0.75rem 1rem;
      cursor: pointer;
      transition: background 0.35s ease, transform 0.2s ease;
      box-shadow: 0 4px 12px rgba(254, 0, 0, 0.4);
      user-select: none;
    }
    button[type="submit"]:hover,
    button[type="submit"]:focus {
      background: linear-gradient(135deg, var(--red-dark), var(--red-darker));
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(175, 0, 0, 0.6);
      outline: none;
    }
    button[type="submit"]:active {
      transform: translateY(0);
      box-shadow: 0 3px 10px rgba(175, 0, 0, 0.5);
    }

    .message {
      border-radius: 8px;
      padding: 1rem 1.2rem;
      margin-bottom: 1.25rem;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      user-select: none;
    }
    .message.success {
      background-color: #d6f0d6;
      color: #1a661a;
      border: 1.5px solid #3aa13a;
    }
    .message.error {
      background-color: #ffd6d6;
      color: #8b0000;
      border: 1.5px solid #fe0000;
    }

    @media (max-width: 520px) {
      main.container {
        padding: 1.5rem 1.5rem;
        max-width: 100%;
      }
      h2 {
        font-size: 1.7rem;
      }
      button[type="submit"] {
        font-size: 1rem;
        padding: 0.65rem 0.85rem;
      }
    }
  </style>

  <script>
    // Simple client-side validation + UX enhancements
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.querySelector('form');
      const inputs = form.querySelectorAll('input, textarea');

      form.addEventListener('submit', (e) => {
        let valid = true;
        inputs.forEach(input => {
          if (!input.checkValidity()) {
            valid = false;
            input.classList.add('invalid');
          } else {
            input.classList.remove('invalid');
          }
        });
        if (!valid) {
          e.preventDefault();
          alert('Please fill in all required fields correctly.');
        }
      });

      // Remove invalid class on input when user modifies field
      inputs.forEach(input => {
        input.addEventListener('input', () => {
          if (input.checkValidity()) {
            input.classList.remove('invalid');
          }
        });
      });
    });
  </script>
</head>
<body>
  <main class="container" role="main" aria-labelledby="addcarheading">
    <h2 id="addcarheading">Add New Car Listing</h2>

    <?php if ($success): ?>
      <div class="message success" role="alert"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="message error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <label for="make">Make</label>
      <input type="text" id="make" name="make" required placeholder="e.g. Toyota" />

      <label for="model">Model</label>
      <input type="text" id="model" name="model" required placeholder="e.g. Corolla" />

      <label for="year">Year</label>
      <input type="number" id="year" name="year" required placeholder="e.g. 2020" min="1900" max="2100" />

      <label for="price">Price (USD)</label>
      <input type="number" id="price" name="price" required placeholder="e.g. 15000" min="0" step="0.01" />

      <label for="mileage">Mileage (km)</label>
      <input type="number" id="mileage" name="mileage" required placeholder="e.g. 75000" min="0" />

      <label for="description">Description</label>
      <textarea id="description" name="description" required placeholder="Add details about the car..."></textarea>

      <label for="image">Car Image</label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required />

      <button type="submit">Add Car</button>
    </form>
  </main>
</body>
</html>
