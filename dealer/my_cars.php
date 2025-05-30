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

    // Delete image from server
    $photo_query = $conn->prepare("SELECT image FROM cars WHERE id = ? AND dealer_id = ?");
    $photo_query->bind_param("ii", $car_id, $dealer_id);
    $photo_query->execute();
    $photo_result = $photo_query->get_result()->fetch_assoc();

    if ($photo_result && !empty($photo_result['image']) && file_exists("../uploads/cars/" . $photo_result['image'])) {
        unlink("../uploads/cars/" . $photo_result['image']);
    }

    // Delete car from DB
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND dealer_id = ?");
    $stmt->bind_param("ii", $car_id, $dealer_id);

    if ($stmt->execute()) {
        $message = "Car deleted successfully.";
    } else {
        $message = "Error deleting car.";
    }
}

// Fetch dealer cars
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
  <link rel="stylesheet" href="../assets/css/style.css" />
  <style>
    /* Root colors blending your palette */
    :root {
      --red-bright: #FE0000;
      --red-dark: #AF0000;
      --cream: #FFFFFA;
      --pink-light: #FF9B9B;
      --dark-blue: #00232A;
      --maroon: #730000;

      --bg-light: var(--cream);
      --bg-table-header: linear-gradient(135deg, var(--pink-light), var(--maroon));
      --border-color: var(--maroon);
      --text-color-dark: var(--dark-blue);
      --btn-edit-bg: var(--red-dark);
      --btn-delete-bg: var(--red-bright);
      --btn-add-bg: var(--dark-blue);
      --btn-text-color: var(--cream);
      --row-hover-bg: #ffe6e6; /* light pink blend */
    }

    /* Reset & base */
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg-light);
      color: var(--text-color-dark);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 15px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 18px rgba(115, 0, 0, 0.25);
    }

    h2 {
      margin-bottom: 25px;
      color: var(--maroon);
      text-align: center;
      letter-spacing: 1.1px;
      font-weight: 700;
    }

    .message {
      padding: 14px 18px;
      background: #dff0d8;
      color: #3c763d;
      margin-bottom: 25px;
      border-radius: 8px;
      font-weight: 600;
      text-align: center;
      box-shadow: inset 0 0 8px rgba(50, 100, 50, 0.2);
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 8px;
      box-shadow: 0 0 6px rgba(115, 0, 0, 0.1);
      background: var(--cream);
      border-radius: 10px;
      overflow: hidden;
    }

    thead tr {
      background: var(--bg-table-header);
      color: var(--btn-text-color);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.3px;
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
      vertical-align: middle;
    }

    tbody tr {
      background: white;
      border-radius: 10px;
      transition: background-color 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    tbody tr:hover {
      background-color: var(--row-hover-bg);
      cursor: default;
    }

    img {
      width: 110px;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(115,0,0,0.15);
      transition: transform 0.3s ease;
    }
    img:hover {
      transform: scale(1.05);
    }

    /* Buttons styling */
    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      color: var(--btn-text-color);
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      display: inline-block;
    }

    .edit-btn {
      background-color: var(--btn-edit-bg);
      box-shadow: 0 3px 8px rgba(175, 0, 0, 0.6);
    }
    .edit-btn:hover,
    .edit-btn:focus {
      background-color: var(--red-bright);
      box-shadow: 0 4px 12px rgba(254, 0, 0, 0.8);
      outline: none;
    }

    .delete-btn {
      background-color: var(--btn-delete-bg);
      box-shadow: 0 3px 8px rgba(254, 0, 0, 0.6);
      margin-left: 8px;
    }
    .delete-btn:hover,
    .delete-btn:focus {
      background-color: var(--red-dark);
      box-shadow: 0 4px 12px rgba(175, 0, 0, 0.8);
      outline: none;
    }

    .add-btn {
      background-color: var(--btn-add-bg);
      box-shadow: 0 3px 8px rgba(0, 35, 42, 0.7);
      float: right;
      margin-bottom: 20px;
      font-size: 1.1rem;
      padding: 10px 22px;
      border-radius: 8px;
    }
    .add-btn:hover,
    .add-btn:focus {
      background-color: var(--maroon);
      box-shadow: 0 4px 14px rgba(115, 0, 0, 0.85);
      outline: none;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
      img {
        width: 90px;
      }
      .btn {
        padding: 7px 14px;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 768px) {
      .container {
        width: 95%;
        padding: 10px;
      }
      table {
        font-size: 0.9rem;
      }
      img {
        width: 75px;
      }
      .add-btn {
        float: none;
        display: block;
        width: 100%;
        text-align: center;
        margin-bottom: 15px;
      }
      .edit-btn, .delete-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
      }
      th, td {
        padding: 8px 10px;
      }
    }

    @media (max-width: 480px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead tr {
        display: none;
      }
      tbody tr {
        margin-bottom: 18px;
        box-shadow: 0 2px 12px rgba(115, 0, 0, 0.1);
        border-radius: 12px;
        background: var(--cream);
        padding: 15px;
      }
      tbody tr td {
        text-align: right;
        padding-left: 50%;
        position: relative;
        border: none;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.95rem;
      }
      tbody tr td:last-child {
        border-bottom: none;
      }
      tbody tr td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 700;
        color: var(--maroon);
        white-space: nowrap;
        font-size: 0.9rem;
      }
      img {
        width: 100%;
        max-width: 200px;
        margin-bottom: 12px;
        border-radius: 12px;
      }
      .btn {
        margin: 6px 4px 0 0;
        width: 48%;
        font-size: 0.9rem;
        padding: 8px 0;
      }
      .add-btn {
        width: 100%;
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>My Inventory</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <a href="
