<?php
include_once '../../../includes/connect-db.php';

$start = $_POST['start_date'] ?? null;
$end = $_POST['end_date'] ?? null;

if (!$start || !$end) {
    die('Invalid date range.');
}

$stmt = $pdo->prepare("SELECT 
        ctrl_no,
        reference_no,
        date(date) as `bought_at`,
        CONCAT(u2.f_name, ' ', u2.l_name) AS customer,
        item.name as item,
        inventory.value as price,
        CASE 
            WHEN is_received = 1 THEN 'claimed'
            WHEN is_received = 0 THEN 'not yet claimed'
        END as status,
        date(received_at) as `claimed_at`,
        CONCAT(u1.f_name, ' ', u1.l_name) AS `handled by`
    FROM inventory 
    INNER JOIN item on inventory.iditem = item.iditem
    INNER JOIN user u1 ON inventory.idofficer = u1.iduser
    INNER JOIN user u2 ON inventory.iduser = u2.iduser
    WHERE date BETWEEN ? AND ?"
);
$stmt->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    die('No records found.');
}

$filename = "inventory_export_{$start}_to_{$end}.csv";

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Pragma: no-cache');
header('Expires: 0');

$fp = fopen('php://output', 'w');

fputcsv($fp, array_keys($rows[0]));

foreach ($rows as $row) {
    fputcsv($fp, $row);
}

fclose($fp);
exit;
