<?php
session_start();
require_once '../includes/db-connect.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include TCPDF if generating PDF
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once '../tcpdf/tcpdf.php';
}

// Handle filters
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$status = $_GET['status'] ?? '';

// Prepare SQL with filters
$where = [];
$params = [];

if ($start_date) {
    $where[] = "t.created_at >= ?";
    $params[] = $start_date . ' 00:00:00';
}
if ($end_date) {
    $where[] = "t.created_at <= ?";
    $params[] = $end_date . ' 23:59:59';
}
if ($status && in_array($status, ['Pending', 'Accepted', 'Rejected', 'Completed'])) {
    $where[] = "t.status = ?";
    $params[] = $status;
}

$where_sql = $where ? "WHERE " . implode(' AND ', $where) : "";

$sql = "SELECT t.id AS trade_id, 
               t.status, 
               t.created_at, 
               t.updated_at,
               u.fullname AS user_name, 
               d.business_name AS dealer_name, 
               c.make, c.model, c.year, c.price
        FROM trades t
        JOIN users u ON t.user_id = u.id
        JOIN dealers d ON t.dealer_id = d.id
        JOIN cars c ON t.car_id = c.id
        $where_sql
        ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);

if ($params) {
    // Dynamically bind params
    $types = str_repeat('s', count($params)); // all params are strings here
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="trade_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Trade ID', 'User', 'Dealer', 'Car', 'Price (Ksh)', 'Status', 'Created At', 'Last Updated']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['trade_id'],
            $row['user_name'],
            $row['dealer_name'],
            $row['make'] . ' ' . $row['model'] . ' (' . $row['year'] . ')',
            number_format($row['price']),
            $row['status'],
            $row['created_at'],
            $row['updated_at']
        ]);
    }
    fclose($output);
    exit();
}

// Export PDF
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Car Trade-In System Admin');
    $pdf->SetTitle('Trade Report');
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    $html = '<h2>Trade Report</h2>';
    $html .= '<table border="1" cellpadding="4">';
    $html .= '<thead><tr style="background-color:#ddd;">
        <th>Trade ID</th><th>User</th><th>Dealer</th><th>Car</th><th>Price (Ksh)</th><th>Status</th><th>Created At</th><th>Last Updated</th>
    </tr></thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . htmlspecialchars($row['trade_id']) . '</td>
            <td>' . htmlspecialchars($row['user_name']) . '</td>
            <td>' . htmlspecialchars($row['dealer_name']) . '</td>
            <td>' . htmlspecialchars($row['make'] . ' ' . $row['model'] . ' (' . $row['year'] . ')') . '</td>
            <td>' . number_format($row['price']) . '</td>
            <td>' . htmlspecialchars($row['status']) . '</td>
            <td>' . htmlspecialchars($row['created_at']) . '</td>
            <td>' . htmlspecialchars($row['updated_at']) . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    $pdf->writeHTML($html);
    $pdf->Output('trade_report.pdf', 'D');
    exit();
}

?>

<?php include '../includes/header.php'; ?>

<div class="container" style="max-width:900px; margin: 40px auto; padding: 20px;">
    <h2>Export Trade & Financial Reports</h2>

    <form method="GET" style="margin-bottom: 20px;">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">

        <label for="end_date" style="margin-left: 20px;">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">

        <label for="status" style="margin-left: 20px;">Trade Status:</label>
        <select name="status" id="status">
            <option value="" <?= $status === '' ? 'selected' : '' ?>>All</option>
            <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Accepted" <?= $status === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
            <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>

        <button type="submit" style="margin-left: 20px;">Filter</button>
    </form>

    <div>
        <a href="<?= $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" 
           class="btn btn-primary" style="margin-right: 10px;">Export as CSV</a>

        <a href="<?= $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>" 
           class="btn btn-danger">Export as PDF</a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
