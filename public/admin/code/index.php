<?php

include_once '../../includes/connect-db.php';
require_once '../../includes/token.php';

if (isset($_GET['download']) && isset($_GET['url'])) {
    // Force download logic
    $qrCodeUrl = urldecode($_GET['url']);
    $fileName = 'user_qrcode.png';

    // Fetch the QR code image content
    $qrImage = file_get_contents($qrCodeUrl);

    if ($qrImage !== false) {
        // Set headers for download
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($qrImage));
        echo $qrImage;
        exit;
    } else {
        die('Error fetching QR code.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body class="bg-gray-100 w-screen min-h-screen flex flex-col">

    <?php include_once '../../includes/partial.php'; ?>

    <div class="mt-4 p-8 rounded-lg text-center">
        <h1 class="text-2xl font-semibold">Your QR Code</h1>

        <!-- QR Code -->
        <div id="qr-container" class="mt-5">
            <?php
            $qrCodeUrl = '';

            if (isset($_SESSION['auth_token'])) {
                $payload = decryptToken($_SESSION['auth_token']);
                if ($payload && isset($payload['user_type'])) {
                    $iduser = $payload['user_id'];

                    $stmt = $pdo->prepare('SELECT * FROM user WHERE iduser=?');
                    $stmt->execute([$iduser]);
                    $user = $stmt->fetch();

                    if (!empty($user['student_id'])) {
                        $qrData = $user['student_id'];
                        $student_num = urlencode($qrData);
                        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data={$student_num}&size=300x300";

                        echo '<img id="qr-image" src="' . htmlspecialchars($qrCodeUrl) . '" alt="User QR Code" class="mx-auto w-60 h-60">';
                    } else {
                        echo '<p class="text-red-500 font-bold">Error: No student number found.</p>';
                    }
                } else {
                    echo '<p class="text-red-500 font-bold">Error: Invalid user session.</p>';
                }
            } else {
                echo '<p class="text-red-500 font-bold">Error: No user authentication detected.</p>';
            }
            ?>
        </div>


        <div class="flex flex-col my-10 items-center">
            <span class="text-xl font-bold"><?= $user['f_name'] ?> <?= $user['l_name'] ?></span>
            <span class="text-gray-700 font-semibold"><?= $user['student_id'] ?></span>
            <span class="text-gray-700"><?= $user['email'] ?></span>
        </div>

        <div class="w-full flex justify-center">
            <!-- Download Button -->
            <?php if (!empty($qrCodeUrl)): ?>
                <a id="download-btn" href="?download=1&url=<?= urlencode($qrCodeUrl) ?>" download="user_qrcode.png"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Download QR Code
                </a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>