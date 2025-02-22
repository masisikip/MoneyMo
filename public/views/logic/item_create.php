<?php
include_once __DIR__ . '/../../../api/includes/connect-db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $value = $_POST['value'];
    $image = $_FILES['image'];

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
            header("Location: ../../item.php?error=1");
            exit();
        }

        $compressed_img = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($compressed_img, $img_resource, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        ob_start();
        imagejpeg($compressed_img, NULL, 60);
        $img_content = ob_get_clean();

        try {
            $stmt = $pdo->prepare("INSERT INTO item (code, name, value, image) VALUES (:code, :name, :value, :image)");
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':image', $img_content, PDO::PARAM_LOB);
            $stmt->execute();
            header("Location: ../../item.php");
            exit();
        } catch (PDOException $e) {
            header("Location: ../../item.php?error=1");
            exit();
        }
    } else {
        header("Location: ../../item.php?error=1");
        exit();
    }
}
?>
