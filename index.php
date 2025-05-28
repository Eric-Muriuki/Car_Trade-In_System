<?php
// index.php - Home page for SwapRide Kenya
session_start(); // Start the session to manage user data
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwapRide Kenya - Trade Smart. Drive Better.</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <h1 class="logo">SwapRide Kenya</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cars.php">Browse Cars</a></li>
                    <li><a href="how_it_works.php">How It Works</a></li>
                    <li><a href="about.php" class="active">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h2>Trade In Your Car with Ease</h2>
            <p>Get fair market value and swap your vehicle with verified dealers and buyers across Kenya.</p>
            <a href="register.php" class="btn hero-btn">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Choose SwapRide?</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <i class="fas fa-car-side"></i>
                    <h3>Smart Valuation</h3>
                    <p>Get your car's market value using local data and dealer input.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-user-check"></i>
                    <h3>Verified Dealers</h3>
                    <p>Only licensed and approved dealers can make trade-in offers.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-exchange-alt"></i>
                    <h3>Seamless Exchange</h3>
                    <p>We handle the paperwork and help you swap your car smoothly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Cars -->
    <section class="featured-cars">
        <div class="container">
            <h2>Featured Cars</h2>
            <div class="car-grid">
                <?php
                // Example: Fetch featured cars from the database
                include('includes/db_connect.php');
                $query = "SELECT * FROM cars WHERE status='available' LIMIT 3";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '
                    <div class="car-card">
                        <img src="uploads/' . $row['photo'] . '" alt="Car Image">
                        <h3>' . $row['make'] . ' ' . $row['model'] . '</h3>
                        <p>Year: ' . $row['year'] . '</p>
                        <p>Ksh ' . number_format($row['price']) . '</p>
                        <a href="car_details.php?id=' . $row['id'] . '" class="btn-small">View</a>
                    </div>';
                }
                ?>
            </div>
            <div class="centered">
                <a href="cars.php" class="btn">View All Cars</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date("Y") ?> SwapRide Kenya. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
