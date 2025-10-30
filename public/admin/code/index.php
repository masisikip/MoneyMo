<?php

include_once '../../includes/connect-db.php';
require_once '../../includes/token.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMo - QR Code</title>
    <?php include_once '../../includes/favicon.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
</head>

<body class="bg-gray-100 w-screen min-h-screen flex flex-col">

    <?php include_once '../../includes/partial.php'; ?>

    <div class="mt-4 p-8 rounded-lg text-center flex flex-col items-center justify-center">
        <!-- QR Code -->
        <div id="qr-container" class="mt-5 flex flex-col items-center justify-center w-fit px-4 bg-gray-100">
            <h1 class="text-2xl my-2 font-bold">MoneyMo</h1>
            <?php
            $qrCodeUrl = '';
            $base64Qr = '';

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

                        // Fetch and base64 encode the QR image
                        $qrImage = @file_get_contents($qrCodeUrl);
                        if ($qrImage !== false) {
                            $base64Qr = 'data:image/png;base64,' . base64_encode($qrImage);
                            echo '<img id="qr-image" class="mt-4" src="' . $base64Qr . '" alt="User QR Code" class="mx-auto w-60 h-60">';
                        } else {
                            echo '<p class="text-red-500 font-bold">Error: Unable to generate QR code.</p>';
                        }
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
            <div class="flex flex-col my-5 items-center">
                <span class="text-xl font-bold"><?= $user['f_name'] ?> <?= $user['l_name'] ?></span>
                <span class="text-gray-700 font-semibold"><?= $user['student_id'] ?></span>
                <span class="text-gray-700"><?= $user['email'] ?></span>
            </div>
        </div>

        <div class="w-full flex justify-center">
            <?php if (!empty($base64Qr)): ?>
                <button id="download-btn"
                    class="px-4 py-2 bg-zinc-700 cursor-pointer text-white rounded-md hover:bg-zinc-800">
                    Download QR Code
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php include_once '../../includes/footer.php'; ?>

</body>

</html>

<script>
    const qrFileName = "QR_<?= $user['student_id'] ?>.png";

    document.querySelector("#download-btn")?.addEventListener("click", function (e) {
        e.preventDefault();

        html2canvas(document.querySelector("#qr-container"), {
            onrendered: function (canvas) {
                const imgDataUrl = canvas.toDataURL("image/png");
                const link = document.createElement("a");
                link.href = imgDataUrl;
                link.download = qrFileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });

    $(document).ready(function () {
        $('#header-title').text('QR Code');
    })
</script>