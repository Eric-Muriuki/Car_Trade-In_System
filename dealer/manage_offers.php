<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

$sql = "
    SELECT t.*, 
           c.make, c.model, c.year, 
           u.full_name AS user_name
    FROM offers t
    JOIN cars c ON t.id = c.id
    JOIN users u ON t.id = u.id
    WHERE t.dealer_id = ?
    ORDER BY t.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Trade Offers</title>
  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --off-white: #FFFFFA;
      --light-red: #FF9B9B;
      --dark-blue: #00232A;
      --dark-red: #730000;
    }

    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--off-white), var(--light-red));
      margin: 0;
      padding: 1rem;
      color: var(--dark-blue);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .container {
      background: var(--off-white);
      max-width: 1000px;
      width: 100%;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(2, 35, 42, 0.2);
      border: 2px solid var(--red-dark);
    }

    h2 {
      color: var(--red-primary);
      text-align: center;
      margin-bottom: 1.5rem;
      text-shadow: 1px 1px 3px var(--dark-red);
    }

    .table-wrapper {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      margin-bottom: 1rem;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 35, 42, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 8px;
      overflow: hidden;
      min-width: 720px;
    }

    thead {
      background: linear-gradient(90deg, var(--red-primary), var(--red-dark));
      color: var(--off-white);
      font-weight: bold;
    }

    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--light-red);
    }

    tbody tr:nth-child(even) {
      background-color: #ffeaea;
    }

    tbody tr:hover {
      background-color: var(--dark-red);
      color: var(--off-white);
      transition: background 0.3s ease;
    }

    .status {
      display: inline-block;
      padding: 0.3em 0.6em;
      font-weight: 600;
      border-radius: 12px;
      font-size: 0.85rem;
      color: white;
      text-transform: capitalize;
    }

    .status.pending {
      background: linear-gradient(45deg, var(--red-primary), var(--light-red));
      box-shadow: 0 0 6px var(--red-primary);
    }

    .status.approved {
      background: linear-gradient(45deg, #00a86b, #004d40);
      box-shadow: 0 0 6px #00a86b;
    }

    .status.rejected {
      background: linear-gradient(45deg, var(--dark-red), var(--red-dark));
      box-shadow: 0 0 6px var(--dark-red);
    }

    .actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn {
      padding: 0.3rem 0.6rem;
      border: none;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: background 0.3s ease;
      font-weight: bold;
    }

    .btn.approve {
      background-color: #00a86b;
      color: white;
    }

    .btn.approve:hover {
      background-color: #007d50;
    }

    .btn.reject {
      background-color: var(--red-dark);
      color: white;
    }

    .btn.reject:hover {
      background-color: var(--dark-red);
    }

    @media (max-width: 600px) {
      body {
        padding: 0.5rem;
      }

      .container {
        padding: 1rem;
      }

      table {
        font-size: 0.85rem;
        min-width: 600px;
      }

      th, td {
        padding: 0.5rem;
      }

      h2 {
        font-size: 1.4rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Manage Trade Offers</h2>

    <?php if ($result->num_rows > 0): ?>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Car</th>
              <th>Owner</th>
              <th>Cash Offer (KES)</th>
              <th>Message</th>
              <th>Status</th>
              <th>Sent On</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($offer = $result->fetch_assoc()): 
              $status_class = strtolower($offer['status']);
            ?>
              <tr>
                <td><?= htmlspecialchars($offer['make'] . ' ' . $offer['model'] . ' (' . $offer['year'] . ')') ?></td>
                <td><?= htmlspecialchars($offer['user_name']) ?></td>
                <td><?= number_format($offer['offer_price'], 2) ?></td>
                <td><?= nl2br(htmlspecialchars($offer['message'])) ?></td>
                <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($offer['status']) ?></span></td>
                <td><?= date('d M Y, H:i', strtotime($offer['created_at'])) ?></td>
                <td>
                  <div class="actions">
                    <form method="POST" action="approve_offer.php" style="display:inline;">
                      <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                      <button type="submit" class="btn approve">Approve</button>
                    </form>
                    <form method="POST" action="reject_offer.php" style="display:inline;">
                      <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                      <button type="submit" class="btn reject">Reject</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>No trade offers found.</p>
    <?php endif; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const wrapper = document.querySelector('.table-wrapper');
      if (!wrapper) return;

      function checkScroll() {
        if (wrapper.scrollWidth > wrapper.clientWidth) {
          wrapper.style.boxShadow = 'inset 10px 0 8px -8px rgba(175, 0, 0, 0.6), inset -10px 0 8px -8px rgba(175, 0, 0, 0.6)';
        } else {
          wrapper.style.boxShadow = 'none';
        }
      }

      checkScroll();
      window.addEventListener('resize', checkScroll);
    });
  </script>
</body>
</html>
