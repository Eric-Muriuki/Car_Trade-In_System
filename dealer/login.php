<?php
// dealer/login.php - Dealer Login Page
session_start();
include('../includes/db_connect.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password, approved FROM dealers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($dealer_id, $hashed_password, $approved);
        $stmt->fetch();

        if (!$approved) {
            $error = "Your registration is pending approval.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['dealer_id'] = $dealer_id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Dealer not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dealer Login | SwapRide Kenya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --primary: #FE0000;
            --primary-dark: #AF0000;
            --accent: #FF9B9B;
            --bg: #00232A;
            --bg-soft: #730000;
            --light: #FFFFFA;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg), var(--bg-soft));
            color: var(--light);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            transition: 0.3s;
        }

        a:hover {
            color: var(--primary);
        }

        .container {
            width: 90%;
            max-width: 500px;
            margin: 0 auto;
            padding: 1rem;
        }

        header, footer {
            background: linear-gradient(90deg, var(--bg-soft), var(--bg));
            padding: 1rem 0;
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: var(--light);
        }

        nav ul {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            list-style: none;
            padding: 0;
            margin-top: 0.5rem;
        }

        nav a {
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-weight: 600;
            color: var(--accent);
        }

        nav a.active,
        nav a:hover {
            background-color: var(--primary);
            color: var(--light);
        }

        .section {
            flex: 1;
            padding: 2rem 0;
        }

        .section h2 {
            font-size: 1.75rem;
            color: var(--accent);
            text-align: center;
            margin-bottom: 1rem;
            text-shadow: 1px 1px var(--bg-soft);
        }

        .error {
            background-color: var(--primary);
            padding: 0.75rem;
            border-radius: 8px;
            color: var(--light);
            text-align: center;
            font-weight: bold;
            margin-bottom: 1.25rem;
        }

        .form-card {
            background: linear-gradient(135deg, var(--bg), var(--bg-soft));
            padding: 2rem 1.5rem;
            border-radius: 14px;
            box-shadow:
                inset 0 0 12px rgba(255, 155, 155, 0.5),
                0 10px 20px rgba(115, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: bold;
            color: var(--accent);
        }

        input[type="email"],
        input[type="password"] {
            padding: 0.7rem;
            font-size: 1rem;
            border: 2px solid var(--accent);
            border-radius: 8px;
            background: var(--light);
            color: var(--bg);
        }

        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 8px var(--primary);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--light);
            font-weight: bold;
            padding: 0.75rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.4s;
            box-shadow:
                0 4px 8px rgba(254, 0, 0, 0.6),
                inset 0 -3px 5px rgba(175, 0, 0, 0.8);
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            box-shadow:
                0 6px 16px rgba(254, 0, 0, 0.7),
                inset 0 3px 6px rgba(255, 155, 155, 0.8);
        }

        footer {
            font-size: 0.9rem;
            color: var(--accent);
        }

        @media (max-width: 600px) {
            .logo {
                font-size: 1.6rem;
            }
            nav ul {
                flex-direction: column;
                gap: 0.5rem;
            }
            .form-card {
                padding: 1.5rem 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input');

            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.style.borderColor = '#FE0000';
                });

                input.addEventListener('blur', () => {
                    if (!input.value.trim()) {
                        input.style.borderColor = '#FF9B9B';
                    }
                });
            });
        });
    </script>
</head>
<body>

<header>
    <div class="container">
        <div class="logo">SwapRide Kenya</div>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php" class="active">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container">
        <h2>Dealer Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-card" novalidate>
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" required autocomplete="email">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required autocomplete="current-password">

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</section>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
