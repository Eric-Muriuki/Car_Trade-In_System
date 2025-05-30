<?php
// admin/login.php - Admin Login
session_start();
include('../includes/db_connect.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_id'] = $id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #00232A;
      color: #FFFFFA;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .container {
      background: #FF9B9B;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      padding: 30px 40px;
      max-width: 400px;
      width: 90%;
      box-sizing: border-box;
    }

    h2 {
      text-align: center;
      color: #730000;
      margin-bottom: 20px;
    }

    .form-card label {
      display: block;
      margin: 12px 0 5px;
      color: #00232A;
      font-weight: 600;
    }

    .form-card input {
      width: 100%;
      padding: 10px;
      border: 1px solid #AF0000;
      border-radius: 6px;
      background-color: #FFFFFA;
      color: #00232A;
      font-size: 16px;
    }

    .btn {
      margin-top: 20px;
      background-color: #AF0000;
      color: #FFFFFA;
      padding: 12px;
      border: none;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background-color: #730000;
    }

    .error {
      background-color: #FE0000;
      color: #FFFFFA;
      padding: 10px;
      border-radius: 6px;
      text-align: center;
      margin-bottom: 15px;
    }

    @media (max-width: 480px) {
      .container {
        padding: 20px;
      }
      .btn {
        padding: 10px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Admin Login</h2>

  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="POST" class="form-card">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <button type="submit" class="btn">Login</button>
  </form>
</div>

</body>
</html>
