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
    <meta charset="UTF-8">
    <title>Dealer Registration</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Dealer Registration</h2>

    <?php if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    } ?>

    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST" class="form-card">
        <label>Business Name *</label>
        <input type="text" name="business_name" required>

        <label>Contact Person *</label>
        <input type="text" name="contact_person" required>

        <label>Email *</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Password *</label>
        <input type="password" name="password" required>

        <label>KRA PIN</label>
        <input type="text" name="kra_pin">

        <button type="submit" class="btn">Register</button>
    </form>
</div>
</body>
</html>
