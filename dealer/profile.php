<?php
session_start();
require_once '../includes/db_connect.php';

// Check dealer login
if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch current dealer info
$sql = "SELECT * FROM dealers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
$dealer = $result->fetch_assoc();

// Handle form submission
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = trim($_POST['business_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $contact_person = trim($_POST['contact_person']);
    $address = trim($_POST['address']);

    if ($business_name && $email && $phone && $contact_person && $address) {
        $update_sql = "UPDATE dealers SET business_name=?, email=?, phone=?, contact_person=?, address=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $business_name, $email, $phone, $contact_person, $address, $dealer_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Refresh dealer info with updated values
            $dealer = [
                'business_name' => $business_name,
                'email' => $email,
                'phone' => $phone,
                'contact_person' => $contact_person,
                'address' => $address
            ];
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dealer Profile</title>
  <style>
    /* Color palette variables */
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
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      padding: 20px;
    }

    .container {
      max-width: 600px;
      background: var(--form-bg);
      margin: 30px auto;
      padding: 30px 35px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(115, 0, 0, 0.2);
    }

    h1 {
      text-align: center;
      color: var(--maroon);
      margin-bottom: 30px;
      letter-spacing: 1.3px;
      font-weight: 700;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    label {
      font-weight: 600;
      color: var(--label-color);
      margin-bottom: 6px;
      display: block;
      font-size: 1rem;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      font-size: 1rem;
      border: 2px solid var(--input-border);
      border-radius: 8px;
      background-color: var(--cream);
      color: var(--dark-blue);
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      resize: vertical;
      font-family: inherit;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
      border-color: var(--red-bright);
      box-shadow: 0 0 8px var(--red-bright);
      outline: none;
      background: #fff;
    }

    textarea {
      min-height: 80px;
      font-family: inherit;
    }

    button {
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

    button:hover,
    button:focus {
      background-color: var(--btn-hover-bg);
      box-shadow: 0 6px 18px rgba(175, 0, 0, 0.85);
      outline: none;
    }

    .message {
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 25px;
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

    /* Responsive design */
    @media (max-width: 600px) {
      .container {
        margin: 20px 15px;
        padding: 25px 20px;
      }

      input[type="text"],
      input[type="email"],
      textarea,
      button {
        font-size: 1rem;
      }

      button {
        padding: 12px 0;
      }
    }

    @media (max-width: 400px) {
      input[type="text"],
      input[type="email"],
      textarea {
        padding: 10px 12px;
      }
    }
  </style>
</head>
<body>
  <div class="container" role="main">
    <h1>Dealer Profile</h1>

    <?php if ($success): ?>
      <div class="message success" role="alert"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="message error" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <label for="business_name">Business Name</label>
      <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($dealer['business_name'] ?? '') ?>" required aria-required="true" />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($dealer['email'] ?? '') ?>" required aria-required="true" />

      <label for="phone">Phone</label>
      <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($dealer['phone'] ?? '') ?>" required aria-required="true" pattern="^\+?[\d\s\-]{7,15}$" title="Enter a valid phone number" />

      <label for="contact_person">Contact Person</label>
      <input type="text" id="contact_person" name="contact_person" value="<?= htmlspecialchars($dealer['contact_person'] ?? '') ?>" required aria-required="true" />

      <label for="address">Address</label>
      <textarea id="address" name="address" required aria-required="true"><?= htmlspecialchars($dealer['address'] ?? '') ?></textarea>

      <button type="submit">Update Profile</button>
    </form>
  </div>

  <script>
    // Basic client-side validation enhancement
    document.querySelector('form').addEventListener('submit', function(e) {
      const phoneInput = this.phone;
      const phonePattern = /^\+?[\d\s\-]{7,15}$/;
      if (!phonePattern.test(phoneInput.value.trim())) {
        e.preventDefault();
        alert('Please enter a valid phone number.');
        phoneInput.focus();
      }
    });
  </script>
</body>
</html>
