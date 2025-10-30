<?php
session_start();
include_once '../../includes/connect-db.php';
require_once '../../includes/token.php';

// Initialize $user with default values to avoid undefined variable errors
$user = [
    'f_name' => 'Guest',
    'l_name' => '',
    'student_id' => '',
    'email' => ''
];

// Check session and overwrite $user if logged in
if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload && isset($payload['user_id'])) {
        $iduser = $payload['user_id'];

        $stmt = $pdo->prepare('SELECT * FROM user WHERE iduser = ?');
        $stmt->execute([$iduser]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbUser) {
            $user = $dbUser;
        }
    }
}
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

    <div class="mt-4 p-6 md:p-8 rounded-lg text-center flex flex-col items-center justify-center">
        <div class="bg-white w-md grid place-content-center rounded-xl shadow-md p-4">
            <!-- QR Code -->
            <div id="qr-container"
                class="flex flex-col items-center justify-center w-fit p-4 bg-white rounded-md">
                <h1 class="text-2xl font-bold mb-4">My QR Code</h1>
                <?php
                $base64Qr = '';

                if (!empty($user['student_id'])) {
                    $qrData = urlencode($user['student_id']);
                    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data={$qrData}&size=300x300";

                    $qrImage = @file_get_contents($qrCodeUrl);
                    if ($qrImage !== false) {
                        $base64Qr = 'data:image/png;base64,' . base64_encode($qrImage);
                        echo '<img id="qr-image" class="mx-auto w-60 h-60 rounded-md" src="' . $base64Qr . '" alt="User QR Code">';
                    } else {
                        echo '<p class="text-red-500 font-bold">Error: Unable to generate QR code.</p>';
                    }
                } else {
                    echo '<p class="text-red-500 font-bold">Error: No student number found.</p>';
                }
                ?>

                <!-- User Info -->
                <div class="flex flex-col items-center pt-4">
                    <span class="text-xl font-bold"><?= htmlspecialchars($user['f_name']) ?>
                        <?= htmlspecialchars($user['l_name']) ?></span>
                    <span class="text-gray-700 font-semibold"><?= htmlspecialchars($user['student_id']) ?></span>
                    <span class="text-gray-700"><?= htmlspecialchars($user['email']) ?></span>
                </div>
            </div>

            <!-- Download Button -->
            <?php if (!empty($base64Qr)): ?>
                <div class="w-full flex justify-center">
                    <button id="download-btn"
                        class="px-12 py-2 mb-4 bg-zinc-700 text-white rounded-full hover:bg-gray-800 cursor-pointer text-sm transition duration-200">
                        Download
                    </button>
                </div>
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
        $('#header-title').text('QR Generation');
    })
</script>