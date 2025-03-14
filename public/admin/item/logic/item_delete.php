<?php
include_once __DIR__ . '/../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['iditem'])) {
        $iditem = $_POST['iditem'];

        try {
            $stmt = $pdo->prepare("DELETE FROM item WHERE iditem = :iditem");
            $stmt->bindParam(':iditem', $iditem);
            $stmt->execute();
            header("Location: ../../item/index.php");
            exit();
        } catch (PDOException $e) {
            header("Location: ../../item/index.php?error=1");
            exit();
        }
    } else {
        header("Location: ../../item/index.php?error=1");
        exit();
    }
}
?>
