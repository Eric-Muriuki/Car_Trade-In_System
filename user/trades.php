<?php
// user/trade_offers.php - View trade offers from dealers/users
session_start();
include('../includes/db_connect.php'); // Adjusted to your connection file path

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Fetch trade offers for this user
$query = "SELECT o.*, c.make AS offered_make, c.model AS offered_model, c.year AS offered_year, c.mileage AS offered_mileage, c.price AS offered_price, c.description AS offered_description, c.image AS offered_image, o.user_car_id
          FROM trades o
          JOIN cars c ON o.user_car_id = c.id
          WHERE o.user_car_id = ?
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Trade Offers | SwapRide Kenya</title>
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
      background: linear-gradient(135deg, var(--red-light) 0%, var(--off-white) 100%);
      color: var(--blue-dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    .container {
      width: 90%;
      max-width: 900px;
      margin: 0 auto;
      padding: 20px 0;
    }
    header.header {
      background: var(--red-primary);
      padding: 15px 0;
      box-shadow: 0 4px 15px rgba(254,0,0,0.7);
      user-select: none;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    header .logo {
      color: var(--off-white);
      margin: 0;
      text-align: center;
      font-size: 2rem;
      font-weight: 900;
      letter-spacing: 0.07em;
      font-family: 'Segoe UI Black', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    nav ul.nav-links {
      list-style: none;
      padding: 0;
      margin: 12px 0 0;
      display: flex;
      justify-content: center;
      gap: 18px;
      flex-wrap: wrap;
    }
    nav ul.nav-links li a {
      color: var(--off-white);
      text-decoration: none;
      font-weight: 700;
      padding: 9px 20px;
      border-radius: 30px;
      background: var(--red-medium);
      box-shadow: 0 3px 10px rgba(175,0,0,0.65);
      transition: background-color 0.35s ease, box-shadow 0.35s ease;
      user-select: none;
      display: inline-block;
      white-space: nowrap;
    }
    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background: var(--red-dark);
      box-shadow: 0 5px 18px var(--red-dark);
    }
    nav ul.nav-links li a.btn {
      background: var(--blue-dark);
      box-shadow: 0 3px 10px rgba(0,35,42,0.8);
      font-weight: 800;
      letter-spacing: 0.05em;
    }
    nav ul.nav-links li a.btn:hover,
    nav ul.nav-links li a.btn:focus {
      background: var(--red-primary);
      box-shadow: 0 6px 20px var(--red-primary);
      outline: none;
    }

    section.section {
      flex-grow: 1;
      padding: 30px 0 50px;
    }
    h2 {
      margin-bottom: 30px;
      color: var(--red-primary);
      text-align: center;
      font-weight: 900;
      letter-spacing: 0.06em;
      text-shadow: 0 0 6px var(--red-light);
    }

    .offers-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
    }

    .offer-box {
      background: var(--off-white);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(254, 0, 0, 0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: box-shadow 0.3s ease;
      user-select: text;
    }
    .offer-box:hover {
      box-shadow: 0 8px 30px rgba(254, 0, 0, 0.25);
    }
    .offer-box img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 3px solid var(--red-medium);
      border-radius: 16px 16px 0 0;
    }
    .offer-info {
      padding: 18px 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .offer-info h3 {
      margin: 0 0 8px;
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--red-dark);
      text-shadow: 0 0 4px var(--red-light);
    }
    .offer-info p {
      margin: 6px 0;
      font-size: 0.95rem;
      line-height: 1.3;
      color: var(--blue-dark);
    }
    .offer-info strong {
      color: var(--red-primary);
      user-select: none;
    }

    form {
      margin-top: 14px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: flex-start;
    }
    form button.btn {
      flex: 1 1 120px;
      padding: 12px 10px;
      font-weight: 800;
      font-size: 0.9rem;
      border-radius: 25px;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      color: var(--off-white);
      user-select: none;
      box-shadow: 0 4px 14px var(--red-primary);
      background: var(--red-primary);
    }
    form button.btn:hover,
    form button.btn:focus {
      background: var(--red-dark);
      box-shadow: 0 6px 22px var(--red-dark);
      outline: none;
    }
    form button.btn-secondary {
      background: var(--blue-dark);
      box-shadow: 0 4px 14px var(--blue-dark);
    }
    form button.btn-secondary:hover,
    form button.btn-secondary:focus {
      background: var(--red-medium);
      box-shadow: 0 6px 22px var(--red-medium);
      outline: none;
    }

    .status-msg {
      margin-top: 10px;
      font-weight: 700;
      color: var(--red-dark);
      font-style: italic;
      user-select: text;
    }

    p.no-offers {
      text-align: center;
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--red-dark);
      margin-top: 40px;
      user-select: text;
    }

    footer.footer {
      background: var(--red-primary);
      padding: 14px 0;
      text-align: center;
      color: var(--off-white);
      user-select: none;
      margin-top: auto;
      box-shadow: 0 -4px 10px rgba(254,0,0,0.5);
      font-weight: 700;
      letter-spacing: 0.05em;
      font-size: 0.9rem;
    }

    @media (max-width: 640px) {
      nav ul.nav-links {
        flex-direction: column;
        gap: 12px;
      }
      .offers-list {
        grid-template-columns: 1fr;
      }
      .offer-box img {
        height: 220px;
      }
      form {
        flex-direction: column;
      }
      form button.btn,
      form button.btn-secondary {
        flex: none;
        width: 100%;
      }
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Enhance accessibility and interaction with buttons in trade offer forms
      document.querySelectorAll('form button').forEach(button => {
        button.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            button.click();
          }
        });
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
        <li><a href="trade_offers.php" class="active">Trade Offers</a></li>
        <li><a href="my_car.php">My Car</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="section">
  <div class="container">
    <h2>Trade Offers Received</h2>

    <?php if ($result->num_rows > 0): ?>
      <div class="offers-list">
        <?php while ($offer = $result->fetch_assoc()): ?>
          <div class="offer-box">
            <img src="../uploads/<?= htmlspecialchars($offer['offered_image']) ?>" alt="Offered Car Image">
            <div class="offer-info">
              <h3><?= htmlspecialchars($offer['offered_make'] . ' ' . $offer['offered_model']) ?> (<?= intval($offer['offered_year']) ?>)</h3>
              <p><strong>Offered By:</strong> <?= htmlspecialchars($offer['from_user']) ?></p>
              <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($offer['message'])) ?></p>
              <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($offer['status'])) ?></p>

              <?php if ($offer['status'] === 'pending'): ?>
                <form method="post" action="process_offer.php" aria-label="Manage trade offer from <?= htmlspecialchars($offer['from_user']) ?>">
                  <input type="hidden" name="offer_id" value="<?= intval($offer['id']) ?>">
                  <button type="submit" name="action" value="accept" class="btn" aria-label="Accept offer">Accept</button>
                  <button type="submit" name="action" value="counter" class="btn btn-secondary" aria-label="Counter offer">Counter</button>
                </form>
              <?php else: ?>
                <p class="status-msg">You have <?= htmlspecialchars($offer['status']) ?> this offer.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-offers">No trade offers received yet.</p>
    <?php endif; ?>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
