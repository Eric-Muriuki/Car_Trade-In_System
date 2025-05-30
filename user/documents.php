<?php
// user/documents.php - Upload documents
session_start();
include('../includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$upload_dir = "../uploads/documents/";
$message = "";

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $logbook = $_FILES['logbook']['name'];
    $ntsa = $_FILES['ntsa']['name'];
    $service = $_FILES['service']['name'];

    // Generate unique names
    $logbook_path = $upload_dir . uniqid() . "_" . basename($logbook);
    $ntsa_path = $upload_dir . uniqid() . "_" . basename($ntsa);
    $service_path = $upload_dir . uniqid() . "_" . basename($service);

    // Upload files
    if (
        move_uploaded_file($_FILES['logbook']['tmp_name'], $logbook_path) &&
        move_uploaded_file($_FILES['ntsa']['tmp_name'], $ntsa_path) &&
        move_uploaded_file($_FILES['service']['tmp_name'], $service_path)
    ) {
        // Save to DB
        $stmt = $conn->prepare("INSERT INTO documents (user_id, logbook, ntsa_results, service_history) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $logbook_path, $ntsa_path, $service_path);
        $stmt->execute();
        $message = "Documents uploaded successfully!";
    } else {
        $message = "Error uploading documents.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Upload Documents | SwapRide Kenya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      :root {
        --red-primary: #FE0000;
        --red-dark: #AF0000;
        --red-soft: #FF9B9B;
        --red-deep: #730000;
        --whiteish: #FFFFFA;
        --blue-dark: #00232A;
        --shadow-light: rgba(254, 0, 0, 0.2);
        --shadow-medium: rgba(254, 0, 0, 0.4);
        --shadow-dark: rgba(175, 0, 0, 0.5);
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, var(--blue-dark), var(--red-deep));
        color: var(--whiteish);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }

      .container {
        max-width: 900px;
        margin: auto;
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

      .header .container {
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
      }

      /* Section */
      section.section {
        background: var(--whiteish);
        border-radius: 14px;
        padding: 30px 40px;
        margin: 30px auto;
        box-shadow:
          0 8px 24px var(--shadow-medium),
          inset 0 0 30px var(--red-soft);
        color: var(--blue-dark);
        max-width: 900px;
      }

      section.section h2 {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 25px;
        color: var(--red-deep);
        text-shadow: 1px 1px 3px var(--red-soft);
        text-align: center;
      }

      .message {
        text-align: center;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--red-dark);
        text-shadow: 1px 1px 2px var(--red-soft);
      }

      form.form-card {
        display: flex;
        flex-direction: column;
        gap: 18px;
      }

      form.form-card label {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--red-deep);
      }

      form.form-card input[type="file"] {
        padding: 8px;
        border-radius: 8px;
        border: 1.5px solid var(--red-dark);
        font-size: 1rem;
        background: var(--whiteish);
        color: var(--blue-dark);
        transition: border-color 0.3s ease;
      }

      form.form-card input[type="file"]:focus {
        outline: none;
        border-color: var(--red-primary);
        box-shadow: 0 0 8px var(--red-primary);
      }

      form.form-card button.btn {
        background: var(--red-primary);
        color: var(--whiteish);
        border: none;
        padding: 12px 0;
        font-weight: 700;
        font-size: 1.2rem;
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
      }

      form.form-card button.btn:hover,
      form.form-card button.btn:focus {
        background: var(--red-dark);
        box-shadow: 0 0 15px var(--shadow-dark);
        outline: none;
      }

      /* Footer */
      footer.footer {
        background: var(--whiteish);
        color: var(--blue-dark);
        text-align: center;
        padding: 15px 10px;
        box-shadow: 0 -4px 15px var(--shadow-light);
        user-select: none;
        font-weight: 600;
      }

      /* Responsive */
      @media (max-width: 768px) {
        section.section {
          padding: 25px 20px;
          margin: 20px 15px;
        }
        form.form-card input[type="file"] {
          font-size: 0.95rem;
        }
        form.form-card button.btn {
          font-size: 1rem;
        }
        nav ul.nav-links {
          gap: 12px;
        }
      }

      @media (max-width: 480px) {
        header.header .container {
          flex-direction: column;
          gap: 12px;
        }
        nav ul.nav-links {
          flex-direction: column;
          gap: 10px;
          align-items: center;
        }
        section.section {
          padding: 20px 15px;
          margin: 15px 10px;
        }
        form.form-card label {
          font-size: 1rem;
        }
        form.form-card button.btn {
          font-size: 0.95rem;
          padding: 10px 0;
        }
      }
    </style>

    <script>
      // Keyboard accessibility for nav links and buttons
      document.addEventListener('DOMContentLoaded', () => {
        const navLinks = document.querySelectorAll('nav ul.nav-links a, form button.btn');

        navLinks.forEach(el => {
          el.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              el.click();
            }
          });
        });
      });
    </script>
</head>
<body>

<!-- Header -->
<header class="header" role="banner">
    <div class="container">
        <h1 class="logo" aria-label="SwapRide Kenya Logo">SwapRide Kenya</h1>
        <nav role="navigation" aria-label="Primary Navigation">
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="documents.php" class="active" aria-current="page">Upload Documents</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Upload Section -->
<section class="section" role="main" aria-labelledby="uploadDocumentsHeading">
    <div class="container">
        <h2 id="uploadDocumentsHeading">Upload Vehicle Documents</h2>
        <?php if (!empty($message)) echo "<p class='message' role='alert'>$message</p>"; ?>

        <form method="POST" enctype="multipart/form-data" class="form-card" aria-describedby="uploadInstructions">
            <p id="uploadInstructions" style="margin-bottom: 18px; color: var(--red-dark); font-weight: 600;">
                Please upload your vehicle's Logbook, NTSA Check Results, and Service History as PDF or image files.
            </p>

            <label for="logbook">Logbook (PDF/Image):</label>
            <input type="file" name="logbook" id="logbook" accept=".pdf,image/*" required>

            <label for="ntsa">NTSA Check Results (PDF/Image):</label>
            <input type="file" name="ntsa" id="ntsa" accept=".pdf,image/*" required>

            <label for="service">Service History (PDF/Image):</label>
            <input type="file" name="service" id="service" accept=".pdf,image/*" required>

            <button type="submit" class="btn" aria-label="Upload Documents">Upload Documents</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="footer" role="contentinfo">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
