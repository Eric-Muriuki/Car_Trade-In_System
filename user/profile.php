<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch current user data
$stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Basic validation
    if (empty($full_name)) {
        $errors[] = "Fullname is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (!empty($phone) && !preg_match('/^\+?\d{7,15}$/', $phone)) {
        $errors[] = "Phone number is invalid.";
    }

    if (empty($errors)) {
        // Check if email or username already taken by others
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE (email = ? OR full_name = ?) AND id != ?");
        $stmt_check->bind_param("ssi", $email, $full_name, $user_id);
        $stmt_check->execute();
        $check_res = $stmt_check->get_result();
        if ($check_res->num_rows > 0) {
            $errors[] = "Fullname or email already in use by another account.";
        } else {
            $stmt_update = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt_update->bind_param("sssi", $full_name, $email, $phone, $user_id);
            if ($stmt_update->execute()) {
                $success = "Profile updated successfully.";
                // Update current values to show in form
                $user['full_name'] = $full_name;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $errors[] = "Failed to update profile. Try again.";
            }
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } else {
        // Verify current password
        $stmt_pw = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pw->bind_param("i", $user_id);
        $stmt_pw->execute();
        $result_pw = $stmt_pw->get_result();
        $row = $result_pw->fetch_assoc();

        if (!password_verify($current_password, $row['password'])) {
            $errors[] = "Current password is incorrect.";
        } else {
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update_pw = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update_pw->bind_param("si", $new_password_hash, $user_id);
            if ($stmt_update_pw->execute()) {
                $success = "Password changed successfully.";
            } else {
                $errors[] = "Failed to change password. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Profile | SwapRide Kenya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        /* Color palette */
        :root {
            --red-primary: #FE0000;
            --red-medium: #AF0000;
            --red-light: #FF9B9B;
            --red-dark: #730000;
            --blue-dark: #00232A;
            --off-white: #FFFFFA;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--red-light), var(--off-white));
            color: var(--blue-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            width: 90%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px 0;
        }
        header.header {
            background: var(--red-primary);
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(254,0,0,0.6);
            user-select: none;
        }
        header .logo {
            color: var(--off-white);
            margin: 0;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            font-family: 'Segoe UI Black', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        nav ul.nav-links {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        nav ul.nav-links li a {
            color: var(--off-white);
            text-decoration: none;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 25px;
            background: var(--red-medium);
            box-shadow: 0 3px 8px rgba(175,0,0,0.6);
            transition: background-color 0.3s ease;
        }
        nav ul.nav-links li a:hover,
        nav ul.nav-links li a.active {
            background: var(--red-dark);
            box-shadow: 0 5px 15px var(--red-dark);
        }

        section.section {
            flex-grow: 1;
            padding: 25px 0;
        }
        .form-container {
            background: var(--off-white);
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(254,0,0,0.2);
        }

        h2 {
            margin-bottom: 25px;
            color: var(--red-primary);
            text-align: center;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        form label {
            display: block;
            margin: 15px 0 6px;
            font-weight: 700;
            color: var(--red-dark);
            font-size: 1rem;
        }
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="tel"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--red-medium);
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            font-weight: 600;
            color: var(--blue-dark);
            background: var(--off-white);
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus {
            border-color: var(--red-primary);
            outline: none;
            box-shadow: 0 0 8px var(--red-light);
        }

        button {
            margin-top: 30px;
            width: 100%;
            padding: 14px 0;
            background: var(--red-primary);
            color: var(--off-white);
            font-weight: 900;
            font-size: 1.1rem;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(254,0,0,0.5);
            transition: background-color 0.4s ease, box-shadow 0.4s ease;
            user-select: none;
        }
        button:hover,
        button:focus {
            background: var(--red-dark);
            box-shadow: 0 8px 30px var(--red-dark);
            outline: none;
        }

        .messages {
            margin-bottom: 20px;
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.3;
            user-select: text;
        }
        .error {
            background: #FDECEA;
            color: #720000;
            border: 1.5px solid #F99595;
        }
        .success {
            background: #DFF2E1;
            color: #065F46;
            border: 1.5px solid #A5D6A7;
        }

        .section-divider {
            margin: 40px 0 20px;
            border-bottom: 2px solid var(--red-light);
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px 15px;
            }
            button {
                font-size: 1rem;
            }
        }
    </style>
    <script>
      // Client-side validation for forms
      document.addEventListener('DOMContentLoaded', () => {
        const profileForm = document.querySelector('form[name="update_profile"]') || document.forms[0];
        const passwordForm = document.querySelector('form[name="change_password"]') || document.forms[1];

        profileForm.addEventListener('submit', e => {
          const fullName = profileForm.full_name.value.trim();
          const email = profileForm.email.value.trim();
          const phone = profileForm.phone.value.trim();

          let errors = [];

          if (!fullName) errors.push("Fullname is required.");
          if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) errors.push("A valid email is required.");
          if (phone && !phone.match(/^\+?\d{7,15}$/)) errors.push("Phone number is invalid.");

          if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
          }
        });

        passwordForm.addEventListener('submit', e => {
          const current = passwordForm.current_password.value.trim();
          const newPass = passwordForm.new_password.value.trim();
          const confirmPass = passwordForm.confirm_password.value.trim();

          let errors = [];
          if (!current || !newPass || !confirmPass) errors.push("All password fields are required.");
          if (newPass !== confirmPass) errors.push("New password and confirmation do not match.");
          if (newPass.length < 6) errors.push("New password must be at least 6 characters.");

          if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
          }
        });
      });
    </script>
</head>
<body>

<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container form-container">

        <h2>Update Profile</h2>

        <?php if ($errors): ?>
            <div class="messages error" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="messages success" role="alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php" novalidate name="update_profile">
            <label for="full_name">Fullname</label>
            <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

            <label for="phone">Phone (optional)</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <div class="section-divider"></div>

        <h2>Change Password</h2>

        <form method="POST" action="profile.php" novalidate name="change_password">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required minlength="6">

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">

            <button type="submit" name="change_password">Change Password</button>
        </form>

    </div>
</section>

<footer class="footer" style="background: var(--red-primary); padding: 15px 0; text-align:center; color: var(--off-white); user-select:none;">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
