<?php
// user/my_car.php - User's Car Profile Page
session_start();
include('../includes/db_connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Fetch user's submitted cars
$query = "SELECT * FROM cars WHERE owner_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Car Profile | SwapRide Kenya</title>
  <style>
    /* Color palette blending the requested colors */
    :root {
      --red-primary: #FE0000;       /* Bright Red */
      --red-dark: #730000;          /* Dark Red */
      --red-medium: #AF0000;        /* Medium Red */
      --red-light: #FF9B9B;         /* Light Pinkish Red */
      --blue-dark: #00232A;         /* Very Dark Blue */
      --whiteish: #FFFFFA;          /* Near White */
      --shadow-color: rgba(255, 0, 0, 0.3);
    }

    /* Reset and base */
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--red-light), var(--whiteish));
      color: var(--blue-dark);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    a {
      color: var(--red-dark);
      text-decoration: none;
      transition: color 0.3s ease;
    }
    a:hover,
    a:focus {
      color: var(--red-primary);
      outline: none;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 1rem 1.25rem;
      width: 95%;
    }

    /* Header */
    header.header {
      background: var(--red-primary);
      color: var(--whiteish);
      padding: 1rem 0;
      box-shadow: 0 4px 10px var(--shadow-color);
      user-select: none;
      flex-shrink: 0;
    }
    header .logo {
      margin: 0;
      font-weight: 900;
      font-size: 2rem;
      text-shadow: 1px 1px 2px var(--red-dark);
    }
    nav ul.nav-links {
      list-style: none;
      display: flex;
      gap: 1rem;
      padding-left: 0;
      margin-top: 0.5rem;
      flex-wrap: wrap;
    }
    nav ul.nav-links li a {
      padding: 0.5rem 1rem;
      border-radius: 25px;
      background: var(--red-medium);
      box-shadow: 0 3px 8px var(--shadow-color);
      font-weight: 600;
      display: inline-block;
      user-select: none;
      transition:
        background-color 0.3s ease,
        box-shadow 0.3s ease,
        color 0.3s ease;
    }
    nav ul.nav-links li a.active,
    nav ul.nav-links li a:hover,
    nav ul.nav-links li a:focus {
      background: var(--red-dark);
      color: var(--whiteish);
      box-shadow: 0 6px 15px var(--red-dark);
      outline: none;
    }
    nav ul.nav-links li a.btn {
      background: var(--whiteish);
      color: var(--red-primary);
      font-weight: 700;
      box-shadow: 0 3px 8px rgba(255, 0, 0, 0.4);
      user-select: none;
    }
    nav ul.nav-links li a.btn:hover,
    nav ul.nav-links li a.btn:focus {
      background: var(--red-light);
      color: var(--red-dark);
      box-shadow: 0 6px 15px var(--red-dark);
      outline: none;
    }

    /* Section: My Car Profile */
    section.mycar-section {
      flex-grow: 1;
      background: var(--whiteish);
      padding: 2rem 0;
      box-shadow: inset 0 0 20px rgba(0, 35, 42, 0.05);
      border-radius: 12px;
      margin-bottom: 3rem;
    }
    section.mycar-section h2 {
      color: var(--red-primary);
      font-size: 2rem;
      margin-bottom: 1rem;
      text-align: center;
      text-shadow: 1px 1px 2px var(--red-medium);
      user-select: none;
    }

    .btn-submit {
      display: inline-block;
      margin: 0 auto 2rem;
      padding: 0.75rem 1.8rem;
      font-weight: 700;
      font-size: 1rem;
      background: var(--red-primary);
      color: var(--whiteish);
      border-radius: 30px;
      box-shadow: 0 5px 15px var(--shadow-color);
      text-align: center;
      user-select: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      border: none;
      border: 2px solid transparent;
      display: block;
      width: max-content;
      margin-left: auto;
      margin-right: auto;
    }
    .btn-submit:hover,
    .btn-submit:focus {
      background: var(--red-dark);
      box-shadow: 0 8px 25px var(--red-dark);
      outline: none;
    }

    /* Car list grid */
    .car-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      padding: 0 1rem;
    }

    .car-box {
      background: linear-gradient(135deg, var(--red-light), var(--whiteish));
      border: 2px solid var(--red-medium);
      border-radius: 14px;
      box-shadow: 0 6px 15px rgba(175, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .car-box:hover,
    .car-box:focus-within {
      box-shadow: 0 10px 25px var(--red-dark);
      border-color: var(--red-dark);
      outline: none;
    }

    .car-box img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-bottom: 3px solid var(--red-primary);
      user-select: none;
      transition: transform 0.3s ease;
    }
    .car-box:hover img,
    .car-box:focus-within img {
      transform: scale(1.05);
    }

    .car-info {
      padding: 1rem 1.2rem;
      color: var(--blue-dark);
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
      user-select: text;
    }
    .car-info h3 {
      margin: 0 0 0.4rem;
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--red-primary);
      text-shadow: 1px 1px 1px var(--red-medium);
    }
    .car-info p {
      margin: 0.15rem 0;
      font-size: 1rem;
      font-weight: 500;
    }

    /* Buttons inside car-info */
    .car-info a.btn {
      margin-top: auto;
      background: var(--red-medium);
      color: var(--whiteish);
      text-align: center;
      padding: 0.5rem 1.2rem;
      border-radius: 25px;
      font-weight: 600;
      box-shadow: 0 4px 10px var(--shadow-color);
      user-select: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      display: inline-block;
      margin-right: 0.5rem;
      border: none;
      cursor: pointer;
    }
    .car-info a.btn:hover,
    .car-info a.btn:focus {
      background: var(--red-dark);
      box-shadow: 0 7px 20px var(--red-dark);
      outline: none;
    }

    .car-info a.btn-danger {
      background: var(--red-primary);
      color: var(--whiteish);
      user-select: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .car-info a.btn-danger:hover,
    .car-info a.btn-danger:focus {
      background: #aa0000;
      box-shadow: 0 7px 20px #730000;
      outline: none;
    }

    /* Message when no cars */
    .mycar-section p {
      text-align: center;
      font-size: 1.1rem;
      color: var(--red-dark);
      margin-top: 2rem;
      user-select: none;
    }
    .mycar-section p a {
      font-weight: 700;
      color: var(--red-primary);
    }

    /* Footer */
    footer.footer {
      background: var(--red-dark);
      color: var(--whiteish);
      text-align: center;
      padding: 1rem 0;
      user-select: none;
      flex-shrink: 0;
      box-shadow: 0 -4px 12px var(--red-primary);
      font-size: 0.95rem;
    }

    /* Responsive */
    @media (max-width: 900px) {
      nav ul.nav-links {
        justify-content: center;
        gap: 0.8rem;
      }
      .car-list {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
      }
      .car-box img {
        height: 140px;
      }
    }

    @media (max-width: 600px) {
      header .logo {
        font-size: 1.5rem;
        text-align: center;
      }
      nav ul.nav-links {
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      .btn-submit {
        width: 100%;
        max-width: 280px;
        margin-left: auto;
        margin-right: auto;
        font-size: 1.1rem;
        padding: 0.85rem 0;
      }
      .car-info h3 {
        font-size: 1.1rem;
      }
      .car-info p {
        font-size: 0.9rem;
      }
      .car-box img {
        height: 120px;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<header class="header" role="banner">
  <div class="container">
    <h1 class="logo" aria-label="SwapRide Kenya">SwapRide Kenya</h1>
    <nav role="navigation" aria-label="Primary navigation">
      <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="trade_requests.php">My Requests</a></li>
        <li><a href="my_car.php" class="active" aria-current="page">My Car</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- My Car Profile Section -->
<section class="mycar-section" role="main" aria-label="User's submitted cars">
  <div class="container">
    <h2>My Car Profile</h2>
    <a href="submit_car.php" class="btn-submit" role="button" aria-label="Submit new car">Submit New Car</a>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="car-list">
        <?php while ($car = mysqli_fetch_assoc($result)): ?>
          <article class="car-box" tabindex="0" aria-label="Car: <?= htmlspecialchars($car['make'] . ' ' . $car['model'] . ', year ' . $car['year']) ?>">
            <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" alt="Image of <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">
            <div class="car-info">
              <h3><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> (<?= $car['year'] ?>)</h3>
              <p><strong>Price:</strong> KES <?= number_format($car['price']) ?></p>
              <p><strong>Condition:</strong> <?= htmlspecialchars($car['car_condition']) ?></p>
              <p><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
              <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn" aria-label="Edit car <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">Edit</a>
              <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this car?');" aria-label="Delete car <?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">Delete</a>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p>You have not submitted any cars yet. <a href="submit_car.php">Submit one now</a>.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
  </div>
</footer>

<script>
  // No extra JS needed for now as layout and responsiveness are pure CSS-based
  // You can add interactive scripts here if needed in future
</script>

</body>
</html>
