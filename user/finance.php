<?php
session_start();
include('../includes/db_connect.php');

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch finance partners and their plans
$query = "
SELECT fp.id AS partner_id, fp.name AS partner_name, fp.contact_info,
       fp.logo, fp.description,
       fpl.id AS plan_id, fpl.plan_name, fpl.duration_months, fpl.interest_rate,
       fpl.min_amount, fpl.max_amount
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --red-primary: #FE0000;
            --red-dark: #AF0000;
            --red-soft: #FF9B9B;
            --red-deep: #730000;
            --whiteish: #FFFFFA;
            --blue-dark: #00232A;
            --shadow-light: rgba(254, 0, 0, 0.15);
            --shadow-medium: rgba(254, 0, 0, 0.3);
            --shadow-dark: rgba(175, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--blue-dark), var(--red-deep));
            color: var(--whiteish);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            line-height: 1.5;
        }

        .container {
            max-width: 960px;
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
        }

        section.section h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 25px;
            color: var(--red-deep);
            text-shadow: 1px 1px 3px var(--red-soft);
            text-align: center;
        }

        .finance-partner-card {
            background: var(--red-soft);
            border-radius: 12px;
            margin-bottom: 30px;
            padding: 20px 25px;
            box-shadow: 0 6px 15px var(--shadow-light);
            color: var(--red-deep);
            transition: transform 0.3s ease;
        }

        .finance-partner-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px var(--shadow-medium);
        }

        .partner-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }

        .partner-logo {
            max-height: 50px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px var(--shadow-dark);
            user-select: none;
        }

        .partner-header h3 {
            font-size: 1.6rem;
            margin: 0;
            font-weight: 700;
            color: var(--red-deep);
            text-shadow: 1px 1px 2px var(--red-primary);
        }

        .finance-partner-card p {
            font-size: 1rem;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }

        table.plans-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        table.plans-table thead {
            background: var(--red-primary);
            color: var(--whiteish);
            box-shadow: 0 2px 10px var(--shadow-medium);
        }

        table.plans-table th,
        table.plans-table td {
            padding: 12px 10px;
            border: 1px solid var(--red-dark);
            text-align: center;
        }

        table.plans-table tbody tr:nth-child(even) {
            background: var(--red-soft);
        }

        table.plans-table tbody tr:nth-child(odd) {
            background: var(--whiteish);
        }

        table.plans-table tbody tr:hover {
            background: var(--red-primary);
            color: var(--whiteish);
            cursor: default;
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

            .finance-partner-card {
                padding: 15px 20px;
            }

            table.plans-table th,
            table.plans-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
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
            .finance-partner-card p {
                font-size: 0.95rem;
            }
            table.plans-table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<header class="header" role="banner">
    <div class="container">
        <h1 class="logo">SwapRide Kenya</h1>
        <nav role="navigation" aria-label="Main navigation">
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="finance.php" class="active" aria-current="page">Finance Options</a></li>
                <li><a href="documents.php">Upload Documents</a></li>
                <li><a href="logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="section" role="main" aria-labelledby="financeHeading">
    <div class="container">
        <h2 id="financeHeading">Car Finance Partners &amp; Repayment Plans</h2>

        <?php if (!empty($finance_data)): ?>
            <?php foreach ($finance_data as $partner): ?>
                <article class="finance-partner-card" role="region" aria-labelledby="partner-<?= htmlspecialchars($partner['name']) ?>">
                    <div class="partner-header">
                        <?php if ($partner['logo']): ?>
                            <img src="../uploads/logos/<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?> Logo" class="partner-logo" loading="lazy" />
                        <?php endif; ?>
                        <h3 id="partner-<?= htmlspecialchars($partner['name']) ?>"><?= htmlspecialchars($partner['name']) ?></h3>
                    </div>
                    <p><?= nl2br(htmlspecialchars($partner['description'])) ?></p>

                    <?php if (!empty($partner['plans'])): ?>
                        <table class="plans-table" summary="Repayment plans offered by <?= htmlspecialchars($partner['name']) ?>">
                            <thead>
                                <tr>
                                    <th scope="col">Plan Name</th>
                                    <th scope="col">Duration (Months)</th>
                                    <th scope="col">Interest Rate (%)</th>
                                    <th scope="col">Min Amount (KES)</th>
                                    <th scope="col">Max Amount (KES)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partner['plans'] as $plan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($plan['plan_name']) ?></td>
                                        <td><?= (int)$plan['duration_months'] ?></td>
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
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No finance partners found.</p>
        <?php endif; ?>
    </div>
</section>

<footer class="footer" role="contentinfo">
    <div class="container">
        <p>&copy; <?= date('Y') ?> SwapRide Kenya. All rights reserved.</p>
    </div>
</footer>

<script>
    // No specific JS needed now, but keeping placeholder for future enhancement
    // Example: Responsive navigation toggling could be added here if menu grows
</script>
</body>
</html>
