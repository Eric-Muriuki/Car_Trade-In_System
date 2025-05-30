<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch cars NOT owned by this dealer and approved by admin
$stmt = $conn->prepare("
    SELECT c.id, c.make, c.model, c.year, c.price, c.car_condition, u.full_name, u.email, c.mileage, c.description
    FROM cars c
    JOIN users u ON c.owner_id = u.id
    WHERE c.is_approved = 1 AND c.owner_id != ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Available Cars for Trade</title>
  <style>
    :root {
      --red-bright: #FE0000;
      --red-dark: #AF0000;
      --cream: #FFFFFA;
      --pink-light: #FF9B9B;
      --dark-blue: #00232A;
      --maroon: #730000;

      --bg-color: var(--cream);
      --card-bg: white;
      --text-color: var(--dark-blue);
      --btn-bg: var(--red-bright);
      --btn-hover-bg: var(--red-dark);
      --border-color: var(--maroon);
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0; padding: 20px;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    h1 {
      text-align: center;
      color: var(--maroon);
      margin-bottom: 30px;
    }

    .car-list {
      max-width: 900px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .car-card {
      background: var(--card-bg);
      border: 2px solid var(--border-color);
      border-radius: 10px;
      padding: 15px 20px;
      box-shadow: 0 0 12px rgba(115,0,0,0.15);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .car-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--maroon);
      margin-bottom: 6px;
    }

    .car-details p {
      margin: 4px 0;
      font-size: 0.95rem;
    }

    .owner-info {
      font-style: italic;
      color: var(--red-dark);
      margin-bottom: 10px;
    }

    .btn-offer {
      background-color: var(--btn-bg);
      color: var(--cream);
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      transition: background-color 0.3s ease;
      user-select: none;
    }

    .btn-offer:hover,
    .btn-offer:focus {
      background-color: var(--btn-hover-bg);
      outline: none;
    }

    @media (max-width: 480px) {
      .car-card {
        padding: 12px 15px;
      }
    }
  </style>
</head>
<body>
  <h1>Available Cars for Trade</h1>

  <section class="car-list" aria-label="List of cars available for trade">
    <?php if ($result->num_rows === 0): ?>
      <p style="text-align:center; font-size:1.1rem;">No cars available at the moment.</p>
    <?php else: ?>
      <?php while ($car = $result->fetch_assoc()): ?>
        <article class="car-card" role="region" aria-labelledby="car-title-<?= $car['id'] ?>">
          <h2 class="car-title" id="car-title-<?= $car['id'] ?>">
            <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?> (<?= htmlspecialchars($car['year']) ?>)
          </h2>

          <div class="car-details">
            <p><strong>Condition:</strong> <?= htmlspecialchars(ucfirst($car['car_condition'])) ?></p>
            <p><strong>Price:</strong> $<?= number_format($car['price'], 2) ?></p>
            <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($car['description'])) ?></p>
          </div>

          <div class="owner-info">
            Owner: <?= htmlspecialchars($car['full_name']) ?> (<?= htmlspecialchars($car['email']) ?>)
          </div>

          <a class="btn-offer" href="send_offer.php?car_id=<?= $car['id'] ?>" role="button" aria-label="Make offer on <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">
            Make Offer
          </a>
        </article>
      <?php endwhile; ?>
    <?php endif; ?>
  </section>
</body>
</html>
