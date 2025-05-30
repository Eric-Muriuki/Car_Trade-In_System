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
    <style>
        :root {
            --red-main: #FE0000;
            --red-dark: #AF0000;
            --red-light: #FF9B9B;
            --cream: #FFFFFA;
            --navy: #00232A;
            --maroon: #730000;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--cream);
            color: var(--navy);
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1rem;
        }

        .header {
            background: var(--red-main);
            color: var(--cream);
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        nav {
            margin-top: 1rem;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            gap: 1rem;
        }

        .nav-links li a {
            text-decoration: none;
            color: var(--cream);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .nav-links li a:hover,
        .nav-links li a.active {
            background: var(--maroon);
        }

        .btn {
            background: var(--navy);
            color: var(--cream);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
        }

        .hero {
            background: linear-gradient(to right, var(--red-dark), var(--red-main));
            color: var(--cream);
            padding: 4rem 0;
            text-align: center;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
        }

        .hero-btn {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .hero-btn:hover {
            background: var(--maroon);
        }

        .features {
            background: var(--cream);
            padding: 3rem 0;
        }

        .features h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .feature-item {
            background: var(--red-light);
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }

        .feature-item i {
            font-size: 2rem;
            color: var(--red-dark);
            margin-bottom: 1rem;
        }

        .featured-cars {
            padding: 3rem 0;
        }

        .featured-cars h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .car-card {
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .car-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .car-card h3 {
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        .car-card p {
            margin: 0.3rem 0;
            color: var(--navy);
        }

        .btn-small {
            display: inline-block;
            margin-top: 0.5rem;
            background: var(--red-dark);
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-small:hover {
            background: var(--red-main);
        }

        .centered {
            text-align: center;
            margin-top: 2rem;
        }

        .footer {
            background: var(--navy);
            color: var(--cream);
            text-align: center;
            padding: 1rem 0;
        }

        @media screen and (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero h2 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <h1 class="logo">SwapRide Kenya</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="cars.php">Browse Cars</a></li>
                    <li><a href="how_it_works.php">How It Works</a></li>
                    <li><a href="about.php">About</a></li>
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
