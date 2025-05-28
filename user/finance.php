<?php
session_start();
include('../db-connect.php');

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch finance partners and their plans
$query = "
SELECT fp.id AS partner_id, fp.name AS partner_name, fp.logo, fp.description,
       fpl.id AS plan_id, fpl.plan_name, fpl.duration_months, fpl.interest_rate, fpl.min_amount, fpl.max_amount
FROM finance_partners fp
LEFT JOIN finance_plans fpl ON fp.id = fpl.partner_id
ORDER BY fp.name, fpl.duration_months
";

$result = mysqli_query($conn, $query);

$finance_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pid = $row['partner_id'];
    if (!isset($finance_data[$pid])) {
        $finance_data[$pid] = [
            'name' => $row['partner_name'],
            'logo' => $row['logo'],
            'description' => $row['description'],
            'plans' => []
        ];
    }
    if ($row['plan_id']) {
        $finance_data[$pid]['plans'][] = [
            'plan_name' => $row['plan_name'],
            'duration_months' => $row['duration_months'],
            'interest_rate' => $row['interest_rate'],
            'min_amount' => $row['min_amount'],
            'max_amount' => $row['max_amount'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Finance Options | SwapRide Kenya</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>

<header class="header">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="finance.php" class="active">Finance Options</a></li>
                <li><a href="documents.php">Upload Documents</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section">
    <div class="container">
        <h2>Car Finance Partners & Repayment Plans</h2>

        <?php if (!empty($finance_data)): ?>
            <?php foreach ($finance_data as $partner): ?>
                <div class="finance-partner-card">
                    <div class="partner-header">
                        <?php if ($partner['logo']): ?>
                            <img src="../uploads/logos/<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?> Logo" class="partner-logo" />
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($partner['name']) ?></h3>
                    </div>
                    <p><?= nl2br(htmlspecialchars($partner['description'])) ?></p>

                    <?php if (!empty($partner['plans'])): ?>
                        <table class="plans-table">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Duration (Months)</th>
                                    <th>Interest Rate (%)</th>
                                    <th>Min Amount (KES)</th>
                                    <th>Max Amount (KES)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partner['plans'] as $plan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($plan['plan_name']) ?></td>
                                        <td><?= $plan['duration_months'] ?></td>
                                        <td><?= number_format($plan['interest_rate'], 2) ?></td>
                                        <td><?= number_format($plan['min_amount'], 2) ?></td>
                                        <td><?= number_format($plan['max_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p><em>No repayment plans available currently.</em></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No finance partners found.</p>
        <?php endif; ?>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
