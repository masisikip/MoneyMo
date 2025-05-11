<?php
include_once __DIR__ . '/../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['iduser'])) {
        $iduser = $_POST['iduser'];

        try {
            $stmt = $pdo->prepare("DELETE FROM user WHERE iduser = :iduser");
            $stmt->bindParam(':iduser', $iduser);
            $stmt->execute();
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully deleted user'
            ]);
            exit();
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete user'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No user found'
        ]);
        exit();
    }
}
?>
