<?php
// about.php - About Us page for SwapRide Kenya
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SwapRide Kenya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
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

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <h2>About SwapRide Kenya</h2>
            <p>
                SwapRide Kenya is a modern vehicle trade-in and resale platform built for the Kenyan market. Our goal is to simplify car ownership transitions through digital tools, real-time offers, and verified vehicle exchanges.
            </p>
            <p>
                Founded by passionate automotive and tech professionals, SwapRide aims to provide a trustworthy ecosystem where individual car owners and licensed dealers can connect, evaluate, and trade vehicles efficiently.
            </p>

            <div class="mission-vision">
                <div class="card">
                    <i class="fas fa-bullseye"></i>
                    <h3>Our Mission</h3>
                    <p>To streamline the car trade-in process in Kenya by offering fair, fast, and transparent solutions powered by technology.</p>
                </div>
                <div class="card">
                    <i class="fas fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To become Kenya’s leading digital marketplace for vehicle trade-ins, resale, and dealer-consumer vehicle exchange.</p>
                </div>
            </div>

            <h3>What Makes Us Different?</h3>
            <ul class="about-list">
                <li><i class="fas fa-check-circle"></i> Localized car valuation based on market trends and verified data.</li>
                <li><i class="fas fa-check-circle"></i> Only licensed dealers and verified users allowed on the platform.</li>
                <li><i class="fas fa-check-circle"></i> Real-time trade-in offers and secure transactions.</li>
                <li><i class="fas fa-check-circle"></i> Paperwork support and post-trade assistance.</li>
            </ul>
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
