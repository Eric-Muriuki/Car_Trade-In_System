<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer/login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch total listed cars
$sql_cars = "SELECT COUNT(*) AS total_cars FROM cars WHERE dealer_id = ?";
$stmt = $conn->prepare($sql_cars);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$total_cars = $result->fetch_assoc()['total_cars'];

// Fetch total trade-in requests
$sql_trades = "SELECT COUNT(*) AS total_requests FROM offers WHERE dealer_id = ?";
$stmt = $conn->prepare($sql_trades);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$total_requests = $result->fetch_assoc()['total_requests'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dealer Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --red-soft: #FF9B9B;
      --red-deep: #730000;
      --whiteish: #FFFFFA;
      --blue-dark: #00232A;
    }

    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--blue-dark) 0%, var(--red-deep) 100%);
      color: var(--whiteish);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: background-color 0.4s ease;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px 15px;
      width: 90%;
      flex-grow: 1;
    }

    .dashboard-header {
      background: var(--red-dark);
      color: var(--whiteish);
      padding: 20px 0;
      border-bottom: 4px solid var(--red-primary);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow:
        0 4px 10px rgba(0, 0, 0, 0.4),
        inset 0 0 10px var(--red-soft);
      user-select: none;
    }

    .logo {
      font-size: 2.2rem;
      font-weight: 700;
      text-align: center;
      text-shadow: 1px 1px 4px var(--red-deep);
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    .nav-dashboard {
      margin-top: 15px;
    }

    .nav-links {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 14px;
      list-style: none;
    }

    .nav-links li a {
      background: var(--red-deep);
      color: var(--whiteish);
      padding: 11px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      transition:
        background-color 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease,
        transform 0.3s ease;
      box-shadow: 0 4px 8px rgba(254, 0, 0, 0.45);
      display: inline-block;
      min-width: 110px;
      text-align: center;
      user-select: none;
    }

    .nav-links li a:hover,
    .nav-links li a.active {
      background: var(--red-primary);
      color: var(--blue-dark);
      font-weight: 700;
      box-shadow:
        0 0 14px var(--red-primary),
        0 0 28px var(--red-soft);
      transform: scale(1.1);
      outline-offset: 3px;
      outline: 2px solid var(--red-soft);
    }

    .nav-links li a:focus-visible {
      outline: 3px solid var(--red-primary);
      outline-offset: 3px;
      box-shadow:
        0 0 20px var(--red-primary),
        0 0 40px var(--red-soft);
      transform: scale(1.1);
    }

    .main-content {
      background: var(--whiteish);
      color: var(--blue-dark);
      padding: 35px 30px;
      margin: 30px auto;
      border-radius: 16px;
      box-shadow:
        0 15px 30px rgba(175, 0, 0, 0.35),
        inset 0 0 25px var(--red-soft);
      text-align: center;
      transition: box-shadow 0.3s ease;
    }
    .main-content:hover {
      box-shadow:
        0 20px 40px rgba(254, 0, 0, 0.5),
        inset 0 0 35px var(--red-primary);
    }

    .main-content h2 {
      color: var(--red-deep);
      font-weight: 800;
      text-shadow: 1.5px 1.5px 5px var(--red-soft);
      font-size: 2.2rem;
      margin-bottom: 40px;
      user-select: text;
    }

    .dashboard {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
      margin-top: 15px;
    }

    .card {
      background: linear-gradient(135deg, var(--red-soft), var(--red-dark));
      color: var(--whiteish);
      padding: 28px 25px;
      border-radius: 16px;
      flex: 1 1 320px;
      max-width: 380px;
      text-align: center;
      box-shadow:
        0 10px 24px rgba(254, 0, 0, 0.35),
        inset 0 0 14px var(--red-deep);
      transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background-color 0.3s ease;
      cursor: default;
      user-select: none;
      outline-offset: 6px;
    }

    .card:focus-visible {
      outline: 3px solid var(--red-primary);
      outline-offset: 6px;
      box-shadow:
        0 0 28px var(--red-primary),
        inset 0 0 30px var(--red-primary);
      transform: scale(1.07);
    }

    .card:hover {
      transform: translateY(-12px);
      box-shadow:
        0 16px 32px rgba(254, 0, 0, 0.6),
        inset 0 0 28px var(--red-primary);
      background: linear-gradient(135deg, var(--red-primary), var(--red-deep));
    }

    .card h3 {
      font-size: 1.75rem;
      margin-bottom: 14px;
      font-weight: 800;
      text-shadow: 1.2px 1.2px 4px var(--red-deep);
      user-select: text;
    }

    .card p {
      font-size: 3.2rem;
      font-weight: 900;
      text-shadow: 3px 3px 7px var(--blue-dark);
      user-select: text;
    }

    .footer {
      background: var(--red-deep);
      color: var(--red-soft);
      padding: 18px 0;
      text-align: center;
      font-weight: 700;
      box-shadow: inset 0 0 14px var(--red-primary);
      user-select: none;
      font-size: 0.95rem;
      letter-spacing: 0.03em;
    }

    /* Responsive Improvements */
    @media (max-width: 1024px) {
      .dashboard {
        gap: 22px;
      }
      .card {
        flex: 1 1 45%;
        max-width: 45%;
      }
    }

    @media (max-width: 768px) {
      .nav-links {
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }
      .nav-links li a {
        min-width: 160px;
        font-size: 1.1rem;
        padding: 12px 20px;
      }
      .card {
        max-width: 100%;
        flex: 1 1 100%;
      }
      .main-content {
        padding: 25px 20px;
        margin: 20px auto;
      }
      .logo {
        font-size: 1.8rem;
      }
    }

    @media (max-width: 480px) {
      .logo {
        font-size: 1.4rem;
      }
      .card h3 {
        font-size: 1.4rem;
      }
      .card p {
        font-size: 2.6rem;
      }
      .nav-links li a {
        padding: 10px 16px;
        min-width: 140px;
        font-size: 1rem;
      }
      .main-content h2 {
        font-size: 1.8rem;
        margin-bottom: 25px;
      }
    }
  </style>

  <script>
    // Simple fade-in animation on page load for cards
    document.addEventListener('DOMContentLoaded', () => {
      const cards = document.querySelectorAll('.card');
      cards.forEach((card, i) => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
          card.style.opacity = 1;
          card.style.transform = 'translateY(0)';
        }, i * 150);
      });
    });
  </script>
</head>
<body>
  <header class="dashboard-header" role="banner">
    <div class="container">
      <div class="logo" aria-label="SwapRide Kenya Dealer Panel">SwapRide Kenya - Dealer Panel</div>
      <nav class="nav-dashboard" role="navigation" aria-label="Dealer dashboard navigation">
        <ul class="nav-links">
          <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'dashboard.php' ? 'page' : 'false' ?>">Dashboard</a></li>
          <li><a href="dealer_cars.php" class="<?= $current_page == 'dealer_cars.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'my_cars.php' ? 'page' : 'false' ?>">Trade-In Requests</a></li>
          <li><a href="trade_requests.php" class="<?= $current_page == 'trade_requests.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'trade_requests.php' ? 'page' : 'false' ?>">My Inventory</a></li>
          <li><a href="manage_offers.php" class="<?= $current_page == 'manage_offers.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'manage_offers.php' ? 'page' : 'false' ?>">Manage Offers</a></li>
          <li><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'profile.php' ? 'page' : 'false' ?>">Profile</a></li>
          <li><a href="logout.php" class="<?= $current_page == 'logout.php' ? 'active' : '' ?>" aria-current="<?= $current_page == 'logout.php' ? 'page' : 'false' ?>">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="main-content container" role="main">
    <h2 id="dashboardWelcome" style="color: var(--red-deep); font-weight: 700; text-shadow: 1px 1px 3px var(--red-soft);">
      Welcome to Your Dashboard
    </h2>
    <div class="dashboard" aria-labelledby="dashboardWelcome">
      <section class="card" tabindex="0" role="region" aria-labelledby="totalCarsTitle" aria-describedby="totalCarsDesc">
        <h3 id="totalCarsTitle">Total Cars Listed</h3>
        <p id="totalCarsDesc" aria-live="polite"><?= $total_cars ?></p>
      </section>
      <section class="card" tabindex="0" role="region" aria-labelledby="totalRequestsTitle" aria-describedby="totalRequestsDesc">
        <h3 id="totalRequestsTitle">Trade-In Requests</h3>
        <p id="totalRequestsDesc" aria-live="polite"><?= $total_requests ?></p>
      </section>
    </div>
  </main>

  <footer class="footer" role="contentinfo">
    <div class="container">
      <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
