<?php
include_once __DIR__ . '/../../../includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iditem = $_POST['iditem'];
    $name = $_POST['name'];
    $value = $_POST['value'];
    $image = $_FILES['image'];
    $stock = $_POST['stock'];

    $strings = explode(' ', $name);
    $code = "";
    $n = count($strings);
    for ($i = 0; $i < $n; $i++) {
        if ($i < 2) {
            $code .= strtoupper(substr($strings[$i], 0, 2));
        } else {
            $code .= strtoupper(substr($strings[$i], 0, 1));
        }
        if ($i == 1 && $n > 2) {
           $code .= "_";
        }
    }

    $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM item WHERE code = ?");
    $stmt1->execute([$iditem]);
    $exists = $stmt1->fetchColumn();

    if ($exists == 0) {
        // safe to insert
    } else {
        echo "Item already exists!";
        exit();
    }

    if (!empty($image['tmp_name'])) {
        $source = $image['tmp_name'];
        list($width, $height) = getimagesize($source);
        $max_w = 896;
        $max_h = 640;
        $resize_ratio = min($max_w / $width, $max_h / $height);
        $new_width = $width * $resize_ratio;
        $new_height = $height * $resize_ratio;

        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $img_resource = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $img_resource = imagecreatefrompng($source);
        } else {
            echo json_encode(["error" => "Invalid image type. Only JPEG and PNG are allowed."]);
            exit;
        }

        $compressed_img = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($compressed_img, $img_resource, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        ob_start();
        imagejpeg($compressed_img, NULL, 60);
        $img_content = ob_get_clean();

        try {
            $stmt = $pdo->prepare("UPDATE item SET code = :code, name = :name, value = :value, image = :image, stock = :stock WHERE iditem = :iditem");
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':image', $img_content, PDO::PARAM_LOB);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':iditem', $iditem);
            $stmt->execute();
            echo json_encode(["message" => "Item updated successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error: " . $e->getMessage()]);
        }
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE item SET code = :code, name = :name, value = :value, stock = :stock WHERE iditem = :iditem");
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':iditem', $iditem);
            $stmt->execute();
            echo json_encode(["message" => "Item updated successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error: " . $e->getMessage()]);
        }
    }
}
?>