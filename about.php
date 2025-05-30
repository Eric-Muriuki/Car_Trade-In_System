<?php
// about.php - About Us page for SwapRide Kenya
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About Us - SwapRide Kenya</title>

    <!-- Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

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
            line-height: 1.6;
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

        nav ul.nav-links li a.btn {
            background: var(--red-primary);
            color: var(--whiteish);
            border: none;
            cursor: pointer;
            font-weight: 700;
            padding: 8px 18px;
            box-shadow: 0 4px 15px var(--shadow-dark);
            transition: box-shadow 0.3s ease;
        }

        nav ul.nav-links li a.btn:hover {
            box-shadow: 0 6px 20px var(--shadow-dark);
        }

        /* About Section */
        section.about-section {
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

        section.about-section h2 {
            font-weight: 700;
            font-size: 2.4rem;
            margin-bottom: 20px;
            color: var(--red-deep);
            text-shadow: 1px 1px 3px var(--red-soft);
            text-align: center;
        }

        section.about-section p {
            font-size: 1.1rem;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .mission-vision {
            display: flex;
            gap: 30px;
            margin: 35px 0;
            justify-content: center;
            flex-wrap: wrap;
        }

        .mission-vision .card {
            background: var(--red-soft);
            color: var(--red-deep);
            border-radius: 12px;
            box-shadow: 0 6px 15px var(--shadow-light);
            padding: 25px 30px;
            flex: 1 1 300px;
            max-width: 420px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mission-vision .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px var(--shadow-medium);
        }

        .mission-vision .card i {
            font-size: 3rem;
            margin-bottom: 12px;
            color: var(--red-primary);
            text-shadow: 1px 1px 5px var(--red-deep);
        }

        .mission-vision .card h3 {
            font-size: 1.6rem;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .mission-vision .card p {
            font-size: 1rem;
            line-height: 1.4;
        }

        h3 {
            color: var(--red-deep);
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 1px 1px 2px var(--red-soft);
            text-align: center;
        }

        ul.about-list {
            list-style: none;
            max-width: 700px;
            margin: 0 auto 40px auto;
            padding-left: 0;
        }

        ul.about-list li {
            font-size: 1.1rem;
            margin-bottom: 14px;
            padding-left: 30px;
            position: relative;
            color: var(--blue-dark);
        }

        ul.about-list li i {
            position: absolute;
            left: 0;
            top: 3px;
            color: var(--red-primary);
            font-size: 1.3rem;
            text-shadow: 1px 1px 3px var(--red-deep);
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
        }

        /* Responsive */
        @media (max-width: 768px) {
            section.about-section {
                padding: 30px 25px;
                margin: 25px 20px;
            }
            .mission-vision {
                gap: 20px;
            }
            ul.about-list {
                max-width: 100%;
                padding-left: 0;
            }
            ul.about-list li {
                font-size: 1rem;
                padding-left: 25px;
            }
        }

        @media (max-width: 480px) {
            header .container {
                flex-direction: column;
                gap: 12px;
            }
            nav ul.nav-links {
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }
            nav ul.nav-links li a {
                padding: 10px 20px;
                font-size: 1.05rem;
            }
            section.about-section {
                padding: 25px 15px;
                margin: 15px 10px;
            }
            section.about-section h2 {
                font-size: 1.9rem;
            }
            section.about-section p {
                font-size: 1rem;
            }
            .mission-vision .card {
                max-width: 100%;
                padding: 20px 15px;
            }
            h3 {
                font-size: 1.3rem;
            }
            ul.about-list li {
                font-size: 0.95rem;
                padding-left: 22px;
            }
            ul.about-list li i {
                font-size: 1.1rem;
                top: 2px;
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
</head>
<body>

    <!-- Header -->
    <header class="header" role="banner">
        <div class="container">
            <h1 class="logo">SwapRide Kenya</h1>
            <nav role="navigation" aria-label="Main navigation">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cars.php">Browse Cars</a></li>
                    <li><a href="how_it_works.php">How It Works</a></li>
                    <li><a href="about.php" class="active" aria-current="page">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- About Section -->
    <section class="about-section" role="main" aria-labelledby="aboutHeading">
        <div class="container">
            <h2 id="aboutHeading">About SwapRide Kenya</h2>
            <p>
                SwapRide Kenya is a modern vehicle trade-in and resale platform built for the Kenyan market. Our goal is to simplify car ownership transitions through digital tools, real-time offers, and verified vehicle exchanges.
            </p>
            <p>
                Founded by passionate automotive and tech professionals, SwapRide aims to provide a trustworthy ecosystem where individual car owners and licensed dealers can connect, evaluate, and trade vehicles efficiently.
            </p>

            <div class="mission-vision">
                <div class="card" tabindex="0" aria-label="Our Mission">
                    <i class="fas fa-bullseye" aria-hidden="true"></i>
                    <h3>Our Mission</h3>
                    <p>To streamline the car trade-in process in Kenya by offering fair, fast, and transparent solutions powered by technology.</p>
                </div>
                <div class="card" tabindex="0" aria-label="Our Vision">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                    <h3>Our Vision</h3>
                    <p>To become Kenya’s leading digital marketplace for vehicle trade-ins, resale, and dealer-consumer vehicle exchange.</p>
                </div>
            </div>

            <h3>What Makes Us Different?</h3>
            <ul class="about-list">
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Localized car valuation based on market trends and verified data.</li>
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Only licensed dealers and verified users allowed on the platform.</li>
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Real-time trade-in offers and secure transactions.</li>
                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Paperwork support and post-trade assistance.</li>
            </ul>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <p>&copy; <?= date("Y") ?> SwapRide Kenya. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
