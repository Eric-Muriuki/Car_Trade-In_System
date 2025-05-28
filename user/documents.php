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
    <meta charset="UTF-8">
    <title>Upload Documents | SwapRide Kenya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="documents.php" class="active">Upload Documents</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Upload Section -->
<section class="section">
    <div class="container">
        <h2>Upload Vehicle Documents</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST" enctype="multipart/form-data" class="form-card">
            <label>Logbook (PDF/Image):</label>
            <input type="file" name="logbook" required>

            <label>NTSA Check Results (PDF/Image):</label>
            <input type="file" name="ntsa" required>

            <label>Service History (PDF/Image):</label>
            <input type="file" name="service" required>

            <button type="submit" class="btn">Upload Documents</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
