<?php
// user/process_offer.php
session_start();
include('../db-connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offer_id = intval($_POST['offer_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        $update = "UPDATE trade_offers SET status = 'accepted' WHERE id = $offer_id AND to_user_id = {$_SESSION['user_id']}";
        mysqli_query($conn, $update);
    } elseif ($action === 'counter') {
        $update = "UPDATE trade_offers SET status = 'countered' WHERE id = $offer_id AND to_user_id = {$_SESSION['user_id']}";
        mysqli_query($conn, $update);
    }

    header("Location: trade_offers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Process Trade Offer | SwapRide Kenya</title>
  <style>
    /* Color palette */
    :root {
      --red-primary: #FE0000;
      --red-medium: #AF0000;
      --red-light: #FF9B9B;
      --red-dark: #730000;
      --blue-dark: #00232A;
      --off-white: #FFFFFA;
      --shadow-color: rgba(254, 0, 0, 0.3);
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--red-light), var(--off-white));
      color: var(--blue-dark);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding: 1rem;
      text-align: center;
      user-select: none;
    }

    .message-box {
      background: var(--off-white);
      border: 3px solid var(--red-medium);
      border-radius: 16px;
      box-shadow: 0 6px 20px var(--shadow-color);
      padding: 2rem 3rem;
      max-width: 420px;
      width: 100%;
      color: var(--red-dark);
      font-weight: 700;
      font-size: 1.3rem;
      user-select: text;
    }

    .btn-back {
      margin-top: 1.8rem;
      padding: 0.75rem 2rem;
      background: var(--red-primary);
      color: var(--off-white);
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      box-shadow: 0 5px 18px var(--shadow-color);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      text-decoration: none;
      display: inline-block;
    }
    .btn-back:hover,
    .btn-back:focus {
      background: var(--red-dark);
      box-shadow: 0 8px 28px var(--red-dark);
      outline: none;
      color: var(--off-white);
      text-decoration: none;
    }

    @media (max-width: 480px) {
      .message-box {
        padding: 1.5rem 1.5rem;
        font-size: 1.1rem;
      }
      .btn-back {
        width: 100%;
        padding: 0.85rem 0;
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
  <div class="message-box" role="alert" aria-live="assertive">
    <?php if ($action === 'accept'): ?>
      Trade offer #<?= htmlspecialchars($offer_id) ?> has been <span style="color: var(--red-primary);">accepted</span>.
    <?php elseif ($action === 'counter'): ?>
      Trade offer #<?= htmlspecialchars($offer_id) ?> has been <span style="color: var(--red-primary);">countered</span>.
    <?php else: ?>
      Action not recognized.
    <?php endif; ?>
  </div>
  <a href="trade_offers.php" class="btn-back" role="button" aria-label="Go back to trade offers">Back to Trade Offers</a>
<?php else: ?>
  <div class="message-box" role="alert" aria-live="assertive">
    Invalid access. Please submit a valid form.
  </div>
  <a href="trade_offers.php" class="btn-back" role="button" aria-label="Go back to trade offers">Back to Trade Offers</a>
<?php endif; ?>

</body>
</html>
