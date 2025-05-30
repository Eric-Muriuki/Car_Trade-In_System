<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch offers made on user's cars, with dealer info and car info
$sql = "
    SELECT 
        o.id AS offer_id,
        o.offer_price,
        o.message,
        o.status,
        o.created_at,
        o.updated_at,
        c.make,
        c.model,
        c.year,
        d.business_name AS dealer_name,
        d.email AS dealer_email,
        d.phone AS dealer_phone
    FROM offers o
    JOIN cars c ON o.trade_id = c.id
    JOIN dealers d ON o.dealer_id = d.id
    WHERE c.owner_id = ?
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Trade Requests on My Cars</title>
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
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      margin: 0;
      padding: 20px;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    h1 {
      text-align: center;
      color: var(--maroon);
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--table-bg);
      box-shadow: 0 0 15px rgba(115, 0, 0, 0.1);
      border-radius: 12px;
      overflow: hidden;
    }

    thead {
      background-color: var(--maroon);
      color: var(--cream);
    }

    th, td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
      font-size: 1rem;
      vertical-align: middle;
    }

    tbody tr:hover {
      background-color: var(--pink-light);
      color: var(--maroon);
      transition: background-color 0.3s ease;
    }

    .status {
      text-transform: capitalize;
      font-weight: 600;
    }

    .status.pending {
      color: var(--red-bright);
    }

    .status.accepted {
      color: green;
      font-weight: 700;
    }

    .status.declined {
      color: gray;
      font-style: italic;
    }

    .status.countered {
      color: orange;
      font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 700px) {
      table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
      }
      thead tr {
        display: none;
      }
      tbody tr {
        margin-bottom: 20px;
        background: var(--cream);
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(115, 0, 0, 0.1);
      }
      tbody td {
        padding-left: 50%;
        text-align: right;
        position: relative;
        font-size: 0.9rem;
      }
      tbody td::before {
        position: absolute;
        left: 15px;
        width: 45%;
        padding-left: 10px;
        white-space: nowrap;
        font-weight: 700;
        text-align: left;
        color: var(--maroon);
        content: attr(data-label);
      }
      tbody td:last-child {
        border-bottom: none;
      }
    }
  </style>
</head>
<body>
  <h1>Trade Requests on My Cars</h1>

  <?php if ($result->num_rows === 0): ?>
    <p style="text-align:center; font-size:1.2rem;">You have no trade requests at the moment.</p>
  <?php else: ?>
    <table role="table" aria-label="List of trade requests">
      <thead>
        <tr role="row">
          <th role="columnheader">Car</th>
          <th role="columnheader">Offer Price (KES)</th>
          <th role="columnheader">Dealer</th>
          <th role="columnheader">Message</th>
          <th role="columnheader">Status</th>
          <th role="columnheader">Received At</th>
          <th role="columnheader">Last Updated</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($offer = $result->fetch_assoc()): ?>
          <tr role="row">
            <td role="cell" data-label="Car"><?= htmlspecialchars($offer['make'] . ' ' . $offer['model'] . ' (' . $offer['year'] . ')') ?></td>
            <td role="cell" data-label="Offer Price">KES <?= number_format($offer['offer_price'], 2) ?></td>
            <td role="cell" data-label="Dealer">
              <?= htmlspecialchars($offer['dealer_name']) ?><br />
              <small>Email: <?= htmlspecialchars($offer['dealer_email']) ?></small><br />
              <small>Phone: <?= htmlspecialchars($offer['dealer_phone']) ?></small>
            </td>
            <td role="cell" data-label="Message"><?= nl2br(htmlspecialchars($offer['message'])) ?></td>
            <td role="cell" data-label="Status">
              <span class="status <?= htmlspecialchars($offer['status']) ?>">
                <?= htmlspecialchars(ucfirst($offer['status'])) ?>
              </span>
            </td>
            <td role="cell" data-label="Received At"><?= date('d M Y, H:i', strtotime($offer['created_at'])) ?></td>
            <td role="cell" data-label="Last Updated"><?= date('d M Y, H:i', strtotime($offer['updated_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
