<?php
session_start();
require_once '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch current car
$carQuery = $conn->prepare("SELECT * FROM cars WHERE owner_id = ?");
$carQuery->bind_param("i", $userId);
$carQuery->execute();
$carResult = $carQuery->get_result();
$car = $carResult->fetch_assoc();

// Fetch incoming offers
$offerQuery = $conn->prepare("SELECT COUNT(*) AS offer_count FROM offers WHERE dealer_id = ? AND status = 'Pending'");
$offerQuery->bind_param("i", $userId);
$offerQuery->execute();
$offerResult = $offerQuery->get_result();
$offerData = $offerResult->fetch_assoc();

// Fetch active trades
$tradeQuery = $conn->prepare("SELECT COUNT(*) AS trade_count FROM trades WHERE (user_id = ? OR dealer_id = ?) AND status != 'Completed'");
$tradeQuery->bind_param("ii", $userId, $userId);
$tradeQuery->execute();
$tradeResult = $tradeQuery->get_result();
$tradeData = $tradeResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - Car Trade-In System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --red-soft: #FF9B9B;
      --red-deep: #730000;
      --whiteish: #FFFFFA;
      --blue-dark: #00232A;
      --shadow-light: rgba(254, 0, 0, 0.2);
      --shadow-medium: rgba(254, 0, 0, 0.4);
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
      justify-content: center;
      align-items: flex-start;
      padding: 20px;
    }

    .container {
      background: var(--whiteish);
      color: var(--blue-dark);
      max-width: 900px;
      width: 100%;
      border-radius: 14px;
      padding: 30px 40px;
      box-shadow:
        0 8px 24px var(--shadow-medium),
        inset 0 0 30px var(--red-soft);
      transition: box-shadow 0.3s ease;
    }

    .container:hover {
      box-shadow:
        0 12px 36px var(--shadow-dark),
        inset 0 0 40px var(--red-primary);
    }

    h2 {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 25px;
      text-align: center;
      color: var(--red-deep);
      text-shadow: 1px 1px 3px var(--red-soft);
    }

    .dashboard-card {
      background: linear-gradient(135deg, var(--red-soft), var(--red-dark));
      padding: 20px 25px;
      margin-bottom: 20px;
      border-radius: 12px;
      box-shadow:
        0 4px 15px var(--shadow-light),
        inset 0 0 15px var(--red-deep);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover,
    .dashboard-card:focus-within {
      transform: translateY(-6px);
      box-shadow:
        0 10px 28px var(--shadow-dark),
        inset 0 0 25px var(--red-primary);
      outline: none;
    }

    .dashboard-card h3 {
      font-size: 1.5rem;
      color: var(--whiteish);
      margin-bottom: 10px;
      font-weight: 700;
      text-shadow: 1px 1px 2px var(--red-deep);
    }

    .dashboard-card p {
      font-size: 1.1rem;
      color: var(--whiteish);
      margin-bottom: 12px;
      text-shadow: 1px 1px 2px var(--blue-dark);
      line-height: 1.4;
    }

    a.nav-link {
      display: inline-block;
      padding: 8px 18px;
      background: var(--whiteish);
      color: var(--red-dark);
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0 4px 8px var(--shadow-light);
      transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    }

    a.nav-link:hover,
    a.nav-link:focus {
      background: var(--red-primary);
      color: var(--whiteish);
      box-shadow: 0 8px 18px var(--shadow-dark);
      outline: none;
      transform: scale(1.05);
    }

    /* Responsive Styles */

    @media (max-width: 768px) {
      body {
        padding: 15px;
        align-items: center;
      }
      .container {
        padding: 25px 30px;
        width: 95%;
      }
      .dashboard-card {
        padding: 18px 20px;
      }
      h2 {
        font-size: 1.7rem;
        margin-bottom: 20px;
      }
    }

    @media (max-width: 480px) {
      .dashboard-card h3 {
        font-size: 1.3rem;
      }
      .dashboard-card p {
        font-size: 1rem;
      }
      a.nav-link {
        padding: 7px 15px;
        font-size: 0.9rem;
      }
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>

  <script>
    // Accessibility: add keyboard focus styles for nav links
    document.addEventListener('DOMContentLoaded', () => {
      const navLinks = document.querySelectorAll('a.nav-link');

      navLinks.forEach(link => {
        link.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            link.click();
          }
        });
      });
    });
  </script>
</head>
<body>
  <div class="container" role="main" aria-label="User Dashboard">
    <h2>Welcome to Your Dashboard</h2>

    <section class="dashboard-card" tabindex="0" aria-labelledby="myCarHeading" aria-describedby="myCarDesc">
      <h3 id="myCarHeading">My Listed Car</h3>
      <?php if ($car): ?>
        <p id="myCarDesc"><strong>Make:</strong> <?= htmlspecialchars($car['make']) ?><br />
        <strong>Model:</strong> <?= htmlspecialchars($car['model']) ?><br />
        <strong>Year:</strong> <?= htmlspecialchars($car['year']) ?><br />
        <strong>Status:</strong> <?= htmlspecialchars($car['status']) ?></p>
        <a href="my_car.php" class="nav-link" aria-label="Manage your listed car">Manage Car</a>
      <?php else: ?>
        <p id="myCarDesc">You have not listed any car yet.</p>
        <a href="my_car.php" class="nav-link" aria-label="List your car">List My Car</a>
      <?php endif; ?>
    </section>

    <section class="dashboard-card" tabindex="0" aria-labelledby="incomingOffersHeading" aria-describedby="incomingOffersDesc">
      <h3 id="incomingOffersHeading">Incoming Offers</h3>
      <p id="incomingOffersDesc">You have <strong><?= $offerData['offer_count'] ?></strong> new trade offer(s).</p>
      <a href="trade_offers.php" class="nav-link" aria-label="View your incoming trade offers">View Offers</a>
    </section>

    <section class="dashboard-card" tabindex="0" aria-labelledby="activeTradesHeading" aria-describedby="activeTradesDesc">
      <h3 id="activeTradesHeading">Active Trades</h3>
      <p id="activeTradesDesc">You are involved in <strong><?= $tradeData['trade_count'] ?></strong> ongoing trade(s).</p>
      <a href="trades.php" class="nav-link" aria-label="View your active trades">View Trades</a>
    </section>

    <nav aria-label="User quick links" style="margin-top: 30px; text-align: center;">
      <a href="messages.php" class="nav-link" aria-label="View messages">Messages</a>
      <a href="documents.php" class="nav-link" aria-label="View documents">Documents</a>
      <a href="finance.php" class="nav-link" aria-label="View finance options">Finance Options</a>
      <a href="profile.php" class="nav-link" aria-label="View profile">Profile</a>
      <a href="logout.php" class="nav-link" aria-label="Logout">Logout</a>
    </nav>
  </div>
</body>
</html>
