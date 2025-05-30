<?php
// admin/register.php - Admin Registration
include('../includes/db_connect.php');

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If valid, hash password and insert into DB
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Admin registered successfully!";
        } else {
            $errors[] = "Error: Email already exists or server issue.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Registration</title>
<style>
  /* Root color variables for easy blending */
  :root {
    --red-bright: #FE0000;
    --red-dark: #AF0000;
    --red-muted: #730000;
    --background-light: #FFFFFA;
    --pink-light: #FF9B9B;
    --navy-dark: #00232A;
  }

  /* Reset & base */
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0; padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--background-light);
    color: var(--navy-dark);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
  }

  .container {
    background: white;
    max-width: 400px;
    width: 100%;
    border-radius: 10px;
    box-shadow:
      0 0 20px rgba(175, 0, 0, 0.25),
      inset 0 0 10px var(--pink-light);
    padding: 2rem 2.5rem;
    border: 3px solid var(--red-muted);
  }

  h2 {
    text-align: center;
    color: var(--red-dark);
    margin-bottom: 1.5rem;
    text-shadow: 0 0 3px var(--red-bright);
  }

  /* Error & success messages */
  .error, .success {
    border-radius: 5px;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    font-weight: 600;
    box-shadow: 0 0 4px var(--red-muted);
  }

  .error {
    background: var(--pink-light);
    color: var(--red-dark);
    border: 1px solid var(--red-dark);
  }

  .success {
    background: #d5f5e3;
    color: #166534;
    border: 1px solid #16a34a;
  }

  form.form-card {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  label {
    font-weight: 600;
    color: var(--navy-dark);
    text-shadow: 0 0 1px var(--red-muted);
  }

  input[type="text"],
  input[type="email"],
  input[type="password"] {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    border: 2px solid var(--pink-light);
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    outline-offset: 2px;
  }

  input[type="text"]:focus,
  input[type="email"]:focus,
  input[type="password"]:focus {
    border-color: var(--red-bright);
    box-shadow: 0 0 8px var(--red-bright);
    outline: none;
  }

  /* Submit button */
  button.btn {
    padding: 0.85rem 1rem;
    background: linear-gradient(135deg, var(--red-bright), var(--red-dark));
    color: var(--background-light);
    font-size: 1.1rem;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow:
      0 4px 8px rgba(254, 0, 0, 0.4),
      inset 0 -2px 6px rgba(175, 0, 0, 0.7);
    transition: background 0.3s ease, transform 0.15s ease;
  }

  button.btn:hover,
  button.btn:focus {
    background: linear-gradient(135deg, var(--red-dark), var(--red-muted));
    transform: scale(1.05);
    outline: none;
  }

  /* Responsive adjustments */
  @media (max-width: 480px) {
    body {
      padding: 1rem;
    }
    .container {
      padding: 1.5rem 1.75rem;
      border-width: 2px;
    }
    h2 {
      font-size: 1.5rem;
    }
    button.btn {
      font-size: 1rem;
      padding: 0.7rem;
    }
  }
</style>
</head>
<body>

<div class="container" role="main" aria-labelledby="pageTitle">
    <h2 id="pageTitle">Register Admin</h2>

    <?php foreach ($errors as $error) echo "<p class='error' role='alert'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success' role='status'>$success</p>"; ?>

    <form method="POST" class="form-card" novalidate autocomplete="off" aria-describedby="formDesc">
      <div id="formDesc" class="sr-only">Admin registration form. All fields required.</div>

        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required aria-required="true" autocomplete="name" placeholder="Enter full name" />

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required aria-required="true" autocomplete="email" placeholder="Enter email address" />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required aria-required="true" autocomplete="new-password" placeholder="Enter a password" />

        <button type="submit" class="btn" aria-label="Register Admin">Register</button>
    </form>
</div>

<script>
  // Minimal JS to improve form UX on small devices
  document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('invalid', (e) => {
        e.target.setCustomValidity('Please fill out this field correctly.');
      });
      input.addEventListener('input', (e) => {
        e.target.setCustomValidity('');
      });
    });
  });
</script>

</body>
</html>
