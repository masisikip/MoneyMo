<?php
include_once '../../includes/connect-db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM item");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body class="flex flex-col w-screen min-h-screen">
    <?php include_once '../../includes/partial.php' ?>
    <main class="w-full h-full flex flex-col items-center">
        <div class="font-semibold text-xl mt-5">Scan Student</div>
        <!-- QR Code Scanner -->
        <div id="qr-reader" class="mt-5 w-80 border-4 border-transparent transition-colors"></div>
        <p>Scanned Result: <span id="result">None</span></p>

    </main>

    <!-- Items section -->
    <div id="items" class="fixed w-full h-full bg-gray-500/50 top-0 left-0 justify-center items-center hidden">
        <div id="items-main"
            class="mt-4 w-[30rem] h-[30rem] rounded-md flex flex-col bg-white p-4 overflow-y-auto items-center">
            <div class="w-full items-center flex justify-between">
                <span class="text-xl">Select Items to Avail</span>
                <div class="flex items-center px-1">
                    <input id="select-all" type="checkbox">
                    <label for="select-all" class="text-gray-600 ml-2">Select All</label>
                </div>
            </div>

            <form id="item-form" action="logic/payment.php" method="POST" class="w-full flex flex-col mt-6 items-center">
                <div id="item-grid" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <?php foreach ($items as $item): ?>
                        <div class="w-30 h-30 border border-gray-300 bg-white rounded-md flex justify-center items-center relative cursor-pointer item-card"
                            onclick="selectItem(this)">
                            <input type="checkbox" name="iditem[]" value="<?= $item['iditem'] ?>" class="hidden">
                            <div class="h-3/4">
                                <img src="data:image/jpeg;base64,<?= base64_encode($item['image']) ?>"
                                    class="w-full h-full object-cover">
                            </div>

                            <!-- Overlay Price -->
                            <div class="w-full absolute top-0 left-0 text-end text-gray-700 pt-1 px-2 text-xs rounded-t-md">
                                P <?= $item['value'] ?></div>
                            <div
                                class="w-full h-16 absolute bottom-0 left-0 bg-linear-to-t from-[#000000b1] to-transparent text-white text-xs flex justify-center items-end pb-2 rounded-b-md font-semibold">
                                <?= $item['name'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="student_id" name="student_id">
                <button onclick="processPayment(event)"
                    class="w-20 p-1 bg-black rounded-md text-white cursor-pointer hover:bg-zinc-700 mt-4">Submit</button>
            </form>
        </div>
    </div>

    <!-- Receipt Section -->

    <!-- Screen Loader -->
    <div id="loader" class="w-full h-full fixed items-center justify-center top-0 left-0 bg-gray-700/50 hidden">
        <div
    class="w-16 h-16 border-4 border-t-black border-gray-300 rounded-full animate-spin"
    ></div>
    </div>
</body>

</html>

<script>
    function selectItem(element) {
        let item = $(element);
        let checkbox = item.find('input[name="iditem[]"]');

        if (item.hasClass('border-gray-300')) {
            item.removeClass('border-gray-300').addClass('border-black');
            checkbox.prop('checked', true);
        } else {
            item.addClass('border-gray-300').removeClass('border-black');
            checkbox.prop('checked', false);
        }
    }

    function processPayment(event) {
        event.preventDefault(); 

        let form = new FormData($('#item-form')[0]);

        $('#loader').removeClass('hidden').addClass('flex');

        $.ajax({
            url: "logic/payment.php",
            method: 'POST',
            data: form,
            processData: false, 
            contentType: false, 
            success: function (response) {
                $('#loader').addClass('hidden').removeClass('flex');
                $('#items').addClass('hidden'); 
                $('body').append(response); 
                $('#item-form').reset();
            },
            error: function (xhr, status, error) {
                $('#loader').addClass('hidden').removeClass('flex');
                console.error("AJAX Error: ", error);
                console.error("Status: ", status);
                console.error("Response Text: ", xhr.responseText);
            }
        });
    }

    $(document).ready(function () {
        $('#select-all').on('change', function () {
            if ($(this).prop('checked')) {
                $('.item-card').removeClass('border-gray-300').addClass('border-black');
                $('.item-card').find('input[name="iditem[]"]').prop('checked', true);
            } else {
                $('.item-card').addClass('border-gray-300').removeClass('border-black');
                $('.item-card').find('input[name="iditem[]"]').prop('checked', false);
            }
        });

        const qrReader = $("#qr-reader");
        function onScanSuccess(decodedText) {
            let data = decodedText;
            if (decodedText.length > 15) {
                let qrData = decodedText.split(' ~ ');
                
                data = qrData[0].trim();
            } 
            $('#student_id').val(data);
            qrReader.addClass("border-green-500").removeClass('border-transparent');
            setTimeout(() => qrReader.removeClass("border-green-500").addClass('border-transparent'), 500); // Remove after 1 second
            $('#items').removeClass('hidden').addClass('flex');
        }

        function onScanFailure(error) {
            // Optionally log errors for debugging
        }

        const qrScanner = new Html5Qrcode("qr-reader");

        Html5Qrcode.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                const cameraId = cameras[0].id; // Automatically select the first camera
                qrScanner.start(cameraId, { fps: 10, qrbox: 250 }, onScanSuccess, onScanFailure)
                    .then(() => console.log("Camera started successfully"))
                    .catch(err => alert("Error starting camera: " + err));
            } else {
                alert("No camera found.");
            }
        }).catch(function(err) {
            alert("Error accessing cameras: " + err);
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('#items-main').length && $(event.target).closest('#items').length) {
                $('#items').addClass('hidden').removeClass('flex');
            }
        })

    })
</script>