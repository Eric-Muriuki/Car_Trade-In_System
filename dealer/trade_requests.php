<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$message = "";

// Handle car deletion
if (isset($_GET['delete'])) {
    $car_id = intval($_GET['delete']);

    // Fetch image filename
    $photo_query = $conn->prepare("SELECT image FROM cars WHERE id = ? AND dealer_id = ?");
    $photo_query->bind_param("ii", $car_id, $dealer_id);
    $photo_query->execute();
    $photo_result = $photo_query->get_result()->fetch_assoc();
    if ($photo_result && file_exists("../uploads/cars/" . $photo_result['image'])) {
        unlink("../uploads/cars/" . $photo_result['image']);
    }

    // Delete car
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND dealer_id = ?");
    $stmt->bind_param("ii", $car_id, $dealer_id);
    if ($stmt->execute()) {
        $message = "Car deleted successfully.";
    } else {
        $message = "Error deleting car.";
    }
}

// Fetch cars listed by this dealer
$stmt = $conn->prepare("SELECT * FROM cars WHERE dealer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Inventory - Dealer</title>
  <style>
    :root {
      --red-bright: #FE0000;
      --red-dark: #AF0000;
      --cream: #FFFFFA;
      --pink-light: #FF9B9B;
      --dark-blue: #00232A;
      --maroon: #730000;

      --bg-color: var(--cream);
      --text-color: var(--dark-blue);
      --table-bg: white;
      --border-color: var(--maroon);
      --btn-bg: var(--red-bright);
      --btn-hover-bg: var(--red-dark);
      --btn-text-color: var(--cream);
      --success-bg: #d4edda;
      --success-text: #155724;
      --error-bg: #f8d7da;
      --error-text: #721c24;
    }

    * {
      box-sizing: border-box;
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
      max-width: 900px;
      margin: 30px auto;
      background: var(--table-bg);
      padding: 25px 20px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(115, 0, 0, 0.25);
    }

    h2 {
      color: var(--maroon);
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
      font-size: 2rem;
      letter-spacing: 0.03em;
    }

    .message {
      background-color: var(--success-bg);
      color: var(--success-text);
      padding: 15px 20px;
      margin-bottom: 25px;
      border-radius: 8px;
      border-left: 6px solid var(--red-bright);
      font-weight: 600;
      box-shadow: inset 0 0 12px rgba(115, 0, 0, 0.1);
      text-align: center;
    }

    a.btn {
      display: inline-block;
      background-color: var(--btn-bg);
      color: var(--btn-text-color);
      text-decoration: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 20px;
      box-shadow: 0 6px 18px rgba(254, 0, 0, 0.7);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      border: none;
    }

    a.btn:hover,
    a.btn:focus {
      background-color: var(--btn-hover-bg);
      box-shadow: 0 8px 22px rgba(175, 0, 0, 0.85);
      outline: none;
    }

    table.table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0 0 15px rgba(115, 0, 0, 0.1);
      border-radius: 12px;
      overflow: hidden;
      background: var(--cream);
    }

    table.table thead {
      background-color: var(--maroon);
      color: var(--cream);
    }

    table.table th,
    table.table td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
      font-size: 1rem;
      vertical-align: middle;
    }

    table.table tbody tr:hover {
      background-color: var(--pink-light);
      color: var(--maroon);
      cursor: default;
      transition: background-color 0.3s ease;
    }

    table.table th:first-child,
    table.table td:first-child {
      width: 120px;
      text-align: center;
    }

    table.table img {
      width: 110px;
      height: 70px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(115, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }

    table.table img:hover {
      transform: scale(1.05);
    }

    table.table td span {
      font-style: italic;
      color: var(--maroon);
      font-weight: 600;
    }

    /* Action buttons in table */
    .btn.edit-btn,
    .btn.delete-btn {
      padding: 6px 14px;
      font-size: 0.9rem;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(115, 0, 0, 0.25);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      margin-right: 8px;
      user-select: none;
      text-decoration: none;
      color: var(--cream);
      cursor: pointer;
      display: inline-block;
      border: none;
    }

    .btn.edit-btn {
      background-color: var(--dark-blue);
    }

    .btn.edit-btn:hover,
    .btn.edit-btn:focus {
      background-color: var(--maroon);
      box-shadow: 0 6px 15px rgba(115, 0, 0, 0.8);
      outline: none;
    }

    .btn.delete-btn {
      background-color: var(--red-bright);
    }

    .btn.delete-btn:hover,
    .btn.delete-btn:focus {
      background-color: var(--red-dark);
      box-shadow: 0 6px 15px rgba(175, 0, 0, 0.85);
      outline: none;
    }

    /* Responsive design */
    @media (max-width: 900px) {
      .container {
        padding: 20px 15px;
      }

      table.table th,
      table.table td {
        padding: 12px 10px;
        font-size: 0.9rem;
      }

      table.table img {
        width: 90px;
        height: 60px;
      }

      a.btn {
        font-size: 1rem;
        padding: 10px 18px;
      }
    }

    @media (max-width: 600px) {
      table.table,
      table.table thead,
      table.table tbody,
      table.table th,
      table.table td,
      table.table tr {
        display: block;
        width: 100%;
      }

      table.table thead tr {
        display: none;
      }

      table.table tbody tr {
        margin-bottom: 20px;
        border-radius: 12px;
        background-color: var(--cream);
        box-shadow: 0 0 15px rgba(115, 0, 0, 0.1);
        padding: 15px 15px 20px;
      }

      table.table tbody tr td {
        padding-left: 50%;
        text-align: right;
        position: relative;
        font-size: 0.9rem;
      }

      table.table tbody tr td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: 45%;
        padding-left: 10px;
        font-weight: 700;
        text-align: left;
        color: var(--maroon);
      }

      table.table tbody tr td:first-child {
        padding-left: 15px;
        text-align: center;
      }

      table.table tbody tr td img {
        margin-bottom: 10px;
      }

      .btn.edit-btn,
      .btn.delete-btn {
        font-size: 0.85rem;
        padding: 6px 12px;
        margin-right: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container" role="main">
    <h2>My Inventory</h2>

    <?php if ($message): ?>
      <div class="message" role="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <a href="add_car.php" class="btn add-btn" role="button" aria-label="Add New Car">+ Add New Car</a>

    <table class="table" role="table" aria-label="List of your cars">
      <thead>
        <tr role="row">
          <th role="columnheader">Photo</th>
          <th role="columnheader">Make & Model</th>
          <th role="columnheader">Year</th>
          <th role="columnheader">Mileage</th>
          <th role="columnheader">Price (KES)</th>
          <th role="columnheader">Status</th>
          <th role="columnheader">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($car = $result->fetch_assoc()): ?>
            <tr role="row">
              <td role="cell" data-label="Photo">
                <?php if (!empty($car['image'])): ?>
                  <img src="../uploads/cars/<?= htmlspecialchars($car['image']) ?>" alt="Photo of <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>" />
                <?php else: ?>
                  <span>No image</span>
                <?php endif; ?>
              </td>
              <td role="cell" data-label="Make & Model"><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></td>
              <td role="cell" data-label="Year"><?= htmlspecialchars($car['year']) ?></td>
              <td role="cell" data-label="Mileage"><?= number_format($car['mileage']) ?> km</td>
              <td role="cell" data-label="Price (KES)">KES <?= number_format($car['price'], 2) ?></td>
              <td role="cell" data-label="Status"><?= ucfirst($car['status']) ?></td>
              <td role="cell" data-label="Actions">
                <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn edit-btn" aria-label="Edit <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> listing">Edit</a>
                <a href="?delete=<?= $car['id'] ?>" onclick="return confirm('Are you sure you want to delete this car?');" class="btn delete-btn" aria-label="Delete <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> listing">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" role="cell">You have no cars listed yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
