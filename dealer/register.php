<?php
// dealer/register.php - Dealer Registration
include('../includes/db_connect.php');

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form inputs
    $business_name = trim($_POST['business_name']);
    $contact_person = trim($_POST['contact_person']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $kra_pin = trim($_POST['kra_pin']);

    // Basic validations
    if (empty($business_name) || empty($contact_person) || empty($email) || empty($password)) {
        $errors[] = "Please fill in all required fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // If no errors, proceed to register
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert dealer into DB
        $stmt = $conn->prepare("INSERT INTO dealers (business_name, contact_person, email, phone, password, kra_pin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $business_name, $contact_person, $email, $phone, $hashed_password, $kra_pin);

        if ($stmt->execute()) {
            $success = "Registration successful! Wait for admin approval.";
        } else {
            $errors[] = "Error: Email may already be registered.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dealer Registration</title>
    <style>
        :root {
          --red-bright: #FE0000;
          --red-dark: #AF0000;
          --cream: #FFFFFA;
          --pink-light: #FF9B9B;
          --dark-blue: #00232A;
          --maroon: #730000;

          --bg-color: var(--cream);
          --form-bg: white;
          --input-border: var(--maroon);
          --text-color: var(--dark-blue);
          --btn-bg: var(--red-bright);
          --btn-hover-bg: var(--red-dark);
          --success-bg: #d4edda;
          --success-text: #155724;
          --error-bg: #f8d7da;
          --error-text: #721c24;
          --label-color: var(--maroon);
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 450px;
            background: var(--form-bg);
            margin: 40px auto;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(115, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: var(--maroon);
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: 1.4px;
        }

        form.form-card {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        label {
            font-weight: 600;
            color: var(--label-color);
            font-size: 1rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 1rem;
            border: 2px solid var(--input-border);
            border-radius: 8px;
            background-color: var(--cream);
            color: var(--dark-blue);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--red-bright);
            box-shadow: 0 0 8px var(--red-bright);
            outline: none;
            background: #fff;
        }

        button.btn {
            background-color: var(--btn-bg);
            border: none;
            color: var(--cream);
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 12px rgba(254, 0, 0, 0.7);
            user-select: none;
        }

        button.btn:hover,
        button.btn:focus {
            background-color: var(--btn-hover-bg);
            box-shadow: 0 6px 18px rgba(175, 0, 0, 0.85);
            outline: none;
        }

        .error, .success {
            padding: 14px 18px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            box-shadow: inset 0 0 10px rgba(115, 0, 0, 0.2);
        }

        .success {
            background-color: var(--success-bg);
            color: var(--success-text);
        }

        .error {
            background-color: var(--error-bg);
            color: var(--error-text);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .container {
                margin: 30px 15px;
                padding: 30px 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            button.btn {
                font-size: 1rem;
                padding: 12px 14px;
            }
        }

        @media (max-width: 350px) {
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
<div class="container" role="main">
    <h2>Dealer Registration</h2>

    <?php if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error' role='alert'>" . htmlspecialchars($error) . "</p>";
        }
    } ?>

    <?php if (!empty($success)) echo "<p class='success' role='alert'>" . htmlspecialchars($success) . "</p>"; ?>

    <form method="POST" class="form-card" novalidate>
        <label for="business_name">Business Name *</label>
        <input type="text" id="business_name" name="business_name" required aria-required="true" />

        <label for="contact_person">Contact Person *</label>
        <input type="text" id="contact_person" name="contact_person" required aria-required="true" />

        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required aria-required="true" />

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" pattern="^\+?[\d\s\-]{7,15}$" title="Enter a valid phone number" />

        <label for="password">Password *</label>
        <input type="password" id="password" name="password" required aria-required="true" />

        <label for="kra_pin">KRA PIN</label>
        <input type="text" id="kra_pin" name="kra_pin" />

        <button type="submit" class="btn">Register</button>
    </form>
</div>

<script>
  // Simple client-side validation for phone input
  document.querySelector('form').addEventListener('submit', function(e) {
    const phoneInput = this.phone;
    const phonePattern = /^\+?[\d\s\-]{7,15}$/;
    if (phoneInput.value.trim() !== "" && !phonePattern.test(phoneInput.value.trim())) {
      e.preventDefault();
      alert('Please enter a valid phone number.');
      phoneInput.focus();
    }
  });
</script>
</body>
</html>
