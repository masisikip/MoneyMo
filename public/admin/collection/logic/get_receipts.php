<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../../includes/connect-db.php';
include_once __DIR__ . '/../../../includes/token.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['auth_token'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access - Please log in'
    ]);
    exit;
}

$payload = decryptToken($_SESSION['auth_token']);
if (!$payload || !isset($payload['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid session - Please log in again'
    ]);
    exit;
}

try {
    $from = $_POST['from'] ?? null;
    $to = $_POST['to'] ?? null;

    $query = "
        SELECT 
            ctrl_no,
            DATE(date) AS date,
            CONCAT(u1.f_name, ' ', u1.l_name) AS username,
            quantity,
            item.name AS itemname,
            inventory.value,
            inventory.idinventory,
            CONCAT(u2.f_name, ' ', u2.l_name) AS officerName,
            CASE 
                WHEN payment_type = 0 THEN 'Cash'
                WHEN payment_type = 1 THEN 'Gcash'
                ELSE 'Unknown'
            END AS payment_type,
            inventory.is_void
        FROM inventory
        INNER JOIN item ON inventory.iditem = item.iditem
        INNER JOIN user u1 ON inventory.iduser = u1.iduser
        INNER JOIN user u2 ON inventory.idofficer = u2.iduser
    ";

    // Apply date filter if provided
    if ($from && $to) {
        $query .= " WHERE DATE(date) BETWEEN :from AND :to";
    }

    $query .= " ORDER BY date DESC, ctrl_no DESC";

    $stmt = $pdo->prepare($query);

    if ($from && $to) {
        $stmt->bindParam(':from', $from);
        $stmt->bindParam(':to', $to);
    }

    $stmt->execute();
    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $receipts
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
