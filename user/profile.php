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
    if (empty($full_rname)) {
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
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            margin-top: 20px;
            padding: 12px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background: #0056b3;
        }
        .messages {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        .success {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .section-divider {
            margin: 40px 0 20px;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container form-container">

        <h2>Update Profile</h2>

        <?php if ($errors): ?>
            <div class="messages error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="messages success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php" novalidate>
            <label for="full_name">Fullname</label>
            <input type="text" id="fullname" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

            <label for="phone">Phone (optional)</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <div class="section-divider"></div>

        <h2>Change Password</h2>

        <form method="POST" action="profile.php" novalidate>
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

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
