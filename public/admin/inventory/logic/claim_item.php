<?php
include '../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idinventory'])) {
    $idinventory = $_POST['idinventory'];

    // Update inventory status
    $stmt = $pdo->prepare("UPDATE inventory SET is_received = 1, received_at = NOW() WHERE idinventory = ?");
    if ($stmt->execute([$idinventory])) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}
