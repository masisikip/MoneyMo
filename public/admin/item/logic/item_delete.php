<?php
include_once __DIR__ . '/../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['iditem'])) {
        $iditem = $_POST['iditem'];

        try {
            $stmt = $pdo->prepare("DELETE FROM item WHERE iditem = :iditem");
            $stmt->bindParam(':iditem', $iditem);
            $stmt->execute();
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully deleted item'
            ]);
            exit();
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete item'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No item found'
        ]);
        exit();
    }
}
?>
