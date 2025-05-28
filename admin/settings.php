<?php
session_start();
require_once '../includes/db_connect.php';

// Check admin session
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];
$success = "";

// Fetch existing settings
$stmt = $conn->prepare("SELECT config_key, config_value FROM settings");
$stmt->execute();
$result = $stmt->get_result();

$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['config_key']] = $row['config_value'];
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate commission percentage
    $commission = trim($_POST['commission_percentage'] ?? '');
    if ($commission === '' || !is_numeric($commission) || $commission < 0 || $commission > 100) {
        $errors[] = "Commission percentage must be a number between 0 and 100.";
    }

    // Other fields: just sanitize
    $payment_api_key = trim($_POST['payment_api_key'] ?? '');
    $sms_api_key = trim($_POST['sms_api_key'] ?? '');
    $email_smtp_server = trim($_POST['email_smtp_server'] ?? '');

    if (empty($errors)) {
        // Update or insert settings
        $settingsToUpdate = [
            'commission_percentage' => $commission,
            'payment_api_key' => $payment_api_key,
            'sms_api_key' => $sms_api_key,
            'email_smtp_server' => $email_smtp_server,
        ];

        foreach ($settingsToUpdate as $key => $value) {
            // Check if exists
            $checkStmt = $conn->prepare("SELECT id FROM settings WHERE config_key = ?");
            $checkStmt->bind_param("s", $key);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Update
                $updateStmt = $conn->prepare("UPDATE settings SET config_value = ? WHERE config_key = ?");
                $updateStmt->bind_param("ss", $value, $key);
                $updateStmt->execute();
            } else {
                // Insert new
                $insertStmt = $conn->prepare("INSERT INTO settings (config_key, config_value) VALUES (?, ?)");
                $insertStmt->bind_param("ss", $key, $value);
                $insertStmt->execute();
            }
        }

        $success = "Settings updated successfully.";
        // Refresh $settings array
        $settings = $settingsToUpdate;
    }
}

?>

 <style>
        /* Basic responsive navbar styling */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #212529;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            color: #f8f9fa;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }
        .navbar a:hover {
            color: #ffc107;
        }
        .nav-links {
            display: flex;
            flex-wrap: wrap;
        }
        .nav-title {
            font-size: 1.4rem;
            font-weight: bold;
        }
        .container {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-title">
        <i class="fas fa-shield-alt"></i> Admin Panel
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="dealers.php">Dealers</a>
        <a href="cars.php">Listings</a>
        <a href="trades.php">Trade Logs</a>
        <a href="reports.php">Reports</a>
        <a href="messages.php">Support</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container" style="max-width:600px; margin:40px auto; padding:20px;">
    <h2>Platform Settings</h2>

    <?php if ($errors): ?>
        <div style="color:#b00020; background:#f8d7da; padding:10px; border-radius:5px; margin-bottom:15px;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color:#155724; background:#d4edda; padding:10px; border-radius:5px; margin-bottom:15px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="settings.php" novalidate>
        <div style="margin-bottom:15px;">
            <label for="commission_percentage" style="display:block; margin-bottom:5px;">Commission Percentage (%)</label>
            <input type="number" step="0.01" min="0" max="100" name="commission_percentage" id="commission_percentage" 
                   value="<?= htmlspecialchars($settings['commission_percentage'] ?? '') ?>" required
                   style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="payment_api_key" style="display:block; margin-bottom:5px;">Payment API Key</label>
            <input type="text" name="payment_api_key" id="payment_api_key" 
                   value="<?= htmlspecialchars($settings['payment_api_key'] ?? '') ?>"
                   style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="sms_api_key" style="display:block; margin-bottom:5px;">SMS API Key</label>
            <input type="text" name="sms_api_key" id="sms_api_key" 
                   value="<?= htmlspecialchars($settings['sms_api_key'] ?? '') ?>"
                   style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label for="email_smtp_server" style="display:block; margin-bottom:5px;">Email SMTP Server</label>
            <input type="text" name="email_smtp_server" id="email_smtp_server" 
                   value="<?= htmlspecialchars($settings['email_smtp_server'] ?? '') ?>"
                   style="width:100%; padding:8px; box-sizing:border-box;">
        </div>

        <button type="submit" style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            Save Settings
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
