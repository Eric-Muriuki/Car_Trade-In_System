<?php
// car_details.php - Show specific car information
include('db-connect.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid car ID.";
    exit;
}

$car_id = intval($_GET['id']);
$query = "SELECT * FROM cars WHERE id = $car_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Car not found.";
    exit;
}

$car = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> - Car Details | SwapRide Kenya</title>

  <style>
    :root {
      --red-primary: #FE0000;
      --red-dark: #AF0000;
      --red-soft: #FF9B9B;
      --red-deep: #730000;
      --whiteish: #FFFFFA;
      --blue-dark: #00232A;
      --shadow-light: rgba(254, 0, 0, 0.15);
      --shadow-medium: rgba(254, 0, 0, 0.3);
      --shadow-dark: rgba(175, 0, 0, 0.5);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, var(--blue-dark), var(--red-deep));
      color: var(--whiteish);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      line-height: 1.5;
    }

    .container {
      max-width: 960px;
      margin: 0 auto;
      padding: 20px 25px;
    }

    /* Header */
    header.header {
      background: var(--whiteish);
      color: var(--blue-dark);
      box-shadow: 0 4px 15px var(--shadow-light);
      padding: 15px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--red-deep);
      text-shadow: 1px 1px 2px var(--red-soft);
      user-select: none;
    }

    nav ul.nav-links {
      list-style: none;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    nav ul.nav-links li {
      display: inline;
    }

    nav ul.nav-links li a {
      text-decoration: none;
      font-weight: 600;
      color: var(--red-dark);
      padding: 8px 15px;
      border-radius: 8px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    nav ul.nav-links li a:hover,
    nav ul.nav-links li a.active {
      background: var(--red-primary);
      color: var(--whiteish);
      box-shadow: 0 4px 12px var(--shadow-medium);
    }

    /* Car Detail Section */
    section.car-detail-section {
      background: var(--whiteish);
      border-radius: 14px;
      padding: 40px 50px;
      margin: 30px auto;
      box-shadow:
        0 8px 24px var(--shadow-medium),
        inset 0 0 30px var(--red-soft);
      color: var(--blue-dark);
      flex-grow: 1;
    }

    .car-detail-box {
      display: flex;
      gap: 40px;
      align-items: flex-start;
      flex-wrap: wrap;
      justify-content: center;
    }

    .car-detail-img {
      max-width: 400px;
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 10px 25px var(--shadow-dark);
      object-fit: cover;
      user-select: none;
      flex-shrink: 0;
    }

    .car-detail-content {
      max-width: 500px;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .car-detail-content h2 {
      font-size: 2rem;
      color: var(--red-deep);
      text-shadow: 1px 1px 3px var(--red-soft);
    }

    .car-detail-content p {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .car-detail-content strong {
      color: var(--red-primary);
    }

    .btn-submit {
      margin-top: 20px;
      padding: 12px 28px;
      background-color: var(--red-primary);
      color: var(--whiteish);
      text-decoration: none;
      font-weight: 700;
      border-radius: 8px;
      box-shadow: 0 6px 20px var(--shadow-dark);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      align-self: flex-start;
      user-select: none;
    }

    .btn-submit:hover {
      background-color: var(--red-dark);
      box-shadow: 0 8px 25px var(--shadow-dark);
    }

    /* Footer */
    footer.footer {
      background: var(--whiteish);
      color: var(--blue-dark);
      text-align: center;
      padding: 18px 15px;
      box-shadow: 0 -4px 15px var(--shadow-light);
      user-select: none;
      font-weight: 600;
      font-size: 0.9rem;
      margin-top: auto;
    }

    /* Responsive */
    @media (max-width: 900px) {
      section.car-detail-section {
        padding: 30px 25px;
        margin: 25px 20px;
      }
      .car-detail-box {
        flex-direction: column;
        gap: 25px;
        align-items: center;
      }
      .car-detail-content {
        max-width: 100%;
      }
      .car-detail-content h2 {
        font-size: 1.7rem;
        text-align: center;
      }
      .btn-submit {
        width: 100%;
        text-align: center;
        padding: 14px 0;
      }
    }

    @media (max-width: 480px) {
      nav ul.nav-links {
        flex-direction: column;
        gap: 12px;
        align-items: center;
      }
      nav ul.nav-links li a {
        padding: 10px 20px;
        font-size: 1.05rem;
      }
      section.car-detail-section {
        padding: 25px 15px;
        margin: 15px 10px;
      }
      .car-detail-content p {
        font-size: 1rem;
      }
      footer.footer {
        font-size: 0.85rem;
        padding: 15px 10px;
      }
    }
  </style>

  <script>
    // Responsive navigation toggle for mobile
    document.addEventListener('DOMContentLoaded', () => {
      const navLinks = document.querySelector('nav ul.nav-links');
      if (!navLinks) return;

      // Create toggle button
      const toggleBtn = document.createElement('button');
      toggleBtn.setAttribute('aria-label', 'Toggle navigation menu');
      toggleBtn.setAttribute('aria-expanded', 'false');
      toggleBtn.classList.add('nav-toggle-btn');
      toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
      toggleBtn.style.cssText = `
        background: var(--red-primary);
        border: none;
        color: var(--whiteish);
        font-size: 1.6rem;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: none;
        position: absolute;
        top: 18px;
        right: 25px;
        z-index: 1100;
      `;

      // Insert toggle button before nav ul
      const nav = navLinks.parentElement;
      nav.style.position = 'relative';
      nav.insertBefore(toggleBtn, navLinks);

      // Show toggle button on small screens
      function checkResize() {
        if (window.innerWidth <= 768) {
          toggleBtn.style.display = 'block';
          navLinks.style.display = 'none';
        } else {
          toggleBtn.style.display = 'none';
          navLinks.style.display = 'flex';
        }
      }
      checkResize();

      window.addEventListener('resize', checkResize);

      toggleBtn.addEventListener('click', () => {
        const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        toggleBtn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if (navLinks.style.display === 'flex') {
          navLinks.style.display = 'none';
        } else {
          navLinks.style.display = 'flex';
          navLinks.style.flexDirection = 'column';
          navLinks.style.gap = '12px';
        }
      });
    });
  </script>

  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
</head>
<body>

<!--
