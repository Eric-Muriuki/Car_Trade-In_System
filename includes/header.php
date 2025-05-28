<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Car Trade-In System</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
  <style>
    /* Basic navbar styles */
    body {
      margin: 0; font-family: Arial, sans-serif;
      background: #f4f6f8;
    }
    header {
      background-color: #222;
      color: #fff;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header .logo {
      font-size: 1.5rem;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1.2px;
    }
    nav a {
      color: #fff;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }
    nav a:hover {
      color: #ffa500;
    }
    nav {
      display: flex;
      align-items: center;
    }
    .welcome-msg {
      margin-right: 20px;
      font-size: 0.9rem;
      color: #ccc;
    }
  </style>
</head>
<body>
<header>
  <div class="logo"><a href="/index.php" style="color:#fff; text-decoration:none;">CarTradeIn</a></div>
  <nav>
    <?php if (isset($_SESSION['dealer_id'])): ?>
      <span class="welcome-msg">Hello, Dealer!</span>
      <a href="/dealer/dashboard.php">Dashboard</a>
      <a href="/dealer/my_cars.php">My Inventory</a>
      <a href="/dealer/trade_requests.php">Trade Requests</a>
      <a href="/dealer/messages.php">Messages</a>
      <a href="/dealer/profile.php">Profile</a>
      <a href="/dealer/logout.php">Logout</a>
    <?php elseif (isset($_SESSION['user_id'])): ?>
      <span class="welcome-msg">Hello, User!</span>
      <a href="/user/dashboard.php">Dashboard</a>
      <a href="/user/my_car.php">My Car</a>
      <a href="/user/trade_offers.php">Trade Offers</a>
      <a href="/user/messages.php">Messages</a>
      <a href="/user/profile.php">Profile</a>
      <a href="/user/logout.php">Logout</a>
    <?php else: ?>
      <a href="/index.php">Home</a>
      <a href="/cars.php">Browse Cars</a>
      <a href="/about.php">About Us</a>
      <a href="/contact.php">Contact</a>
      <a href="/login.php">Login</a>
      <a href="/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
