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
    <title>MoneyMo - QR Scanner</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body class="flex flex-col w-screen min-h-screen">
    <?php include_once '../../includes/partial.php' ?>
    <main class="w-full h-full flex flex-col items-center min-h-screen">
        <div class="font-bold text-2xl mt-5">Scan QR</div>
        <!-- QR Code Scanner -->
        <div id="qr-reader" class="mt-5 w-72 border-4 border-transparent transition-colors"></div>
        <!-- Manual Type Button -->
        <button id="manual_btn" type="button" onclick="openManualType()"
            class="relative mt-10 w-72 flex items-center justify-center rounded p-2 text-white bg-black hover:bg-gray-800 cursor-pointer">
            <i class="fa-regular fa-keyboard absolute left-4 text-lg"></i>
            <span>Manual Input</span>
        </button>

    </main>

    <!-- Items section -->
    <div id="items" class="fixed w-full h-full bg-gray-500/50 top-0 left-0 justify-center items-center hidden">
        <div id="items-main"
            class="mt-4 w-[20rem] md:w-120 h-120 rounded-md flex flex-col bg-white p-4 overflow-y-auto items-center">
            <div class="w-full items-center flex justify-between">
                <span class="text-xl">Select Items to Avail</span>
                <div class="flex items-center px-1">
                    <input id="select-all" type="checkbox">
                    <label for="select-all" class="text-gray-600 ml-2">Select All</label>
                </div>
            </div>

            <form id="item-form" action="logic/payment.php" method="POST"
                class="w-full flex flex-col mt-6 items-center">
                <div id="item-grid" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <?php foreach ($items as $item):
                        ?>
                        <div data-stock="<?= $item['stock'] ?>"
                            class="w-30 h-30 border-2 border-gray-300 bg-white rounded-md flex justify-center items-center relative cursor-pointer item-card"
                            <?php if ($item['stock'] > 0)
                                echo 'onclick="selectItem(this)"'; ?>>

                            <?php if ($item['stock'] > 0): ?>
                                <input type="checkbox" name="iditem[]" value="<?= $item['iditem'] ?>" class="hidden">
                                <i class="text-green-500 absolute top-1 left-1 fa-solid fa-square-check invisible check"></i>
                            <?php endif; ?>

                            <?php if ($item['stock'] <= 0): ?>
                                <div class="absolute rounded top-0 left-0 w-full h-full bg-gray-700/50">
                                </div>
                                <span
                                    class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30 text-lg text-white font-bold w-full text-center">Out
                                    of Stock</span>
                            <?php endif; ?>
                            <div class="h-3/4">
                                <img src="data:image/jpeg;base64,<?= base64_encode($item['image']) ?>"
                                    class="w-full h-full object-cover">
                            </div>

                            <!-- Overlay Price -->
                            <div class="w-full absolute top-0 left-0 text-end text-gray-700 pt-1 px-2 text-xs rounded-t-md">
                                P <?= $item['value'] ?></div>
                            <div
                                class="w-full h-16 absolute bottom-0 left-0 bg-linear-to-t from-[#000000b1] to-transparent <?php if ($item['stock'] > 0) {
                                    echo 'text-white';
                                } else {
                                    echo 'text-gray-300';
                                } ?> text-xs flex justify-center text-center items-end pb-2 rounded-b-md font-semibold">
                                <?= $item['name'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="student_id" name="student_id">
                <button onclick="processPayment(event)"
                    class="w-1/3 p-1 bg-black rounded text-white cursor-pointer hover:bg-zinc-700 mt-10">Confirm</button>
            </form>
        </div>
    </div>

    <!-- Manual Type Modal -->

    <div id="manual_type_modal"
        class="w-full h-full fixed top-0 left-0 bg-black/40 backdrop-blur-sm justify-center items-center hidden">
        <div class="rounded bg-white p-4 flex flex-col h-48 w-[20rem]">
            <div class="text-xl font-bold w-full text-center mb-4">Manual ID Input</div>
            <label for="student_id_manual" class="font-semibold">Enter Student ID</label>
            <div class="mt-1 w-full">
                <input type="text" id="student_id_manual" name="student_id" placeholder="20XX-X-XXXX"
                    class="p-1 border rounded focus:outline-none w-full">
            </div>
            <div class="w-full flex justify-center mt-auto">
                <button type="button" onclick="submitID()"
                    class="w-1/3 bg-black p-1 rounded hover:bg-gray-800 cursor-pointer text-white text-center">Submit</button>
                <button type="button" onclick="closeManualType()" id="cancel_btn"
                    class="w-1/3 bg-gray-600 p-1 rounded hover:bg-gray-500 cursor-pointer text-white text-center ml-2">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Error User Modal -->
    <div id="error_user_modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-80 flex flex-col items-center shadow-lg">
            <div class="text-red-500 text-6xl mb-3">
                <i class="fa-regular fa-circle-xmark"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-1 text-center">
                Invalid User!
            </h2>
            <p class="text-gray-500 text-sm text-center mb-4">
                Please check student ID and try again.
            </p>
            <button type="button" onclick="closeErrorUser()"
                class="bg-red-500 hover:bg-red-600 text-white font-medium px-5 py-2 rounded-md cursor-pointer">
                Close
            </button>
        </div>
    </div>


    <!-- Screen Loader -->
    <div id="loader" class="w-full h-full fixed items-center justify-center top-0 left-0 bg-gray-700/50 hidden">
        <div class="w-16 h-16 border-4 border-t-black border-gray-300 rounded-full animate-spin"></div>
    </div>
    <?php include_once '../../includes/footer.php'; ?>

</body>

</html>

<script>
    let qrScanner;
    let selectedCameraId = null;

    function selectItem(element) {
        let item = $(element);
        let checkbox = item.find('input[name="iditem[]"]');

        if (item.hasClass('border-gray-300')) {
            item.removeClass('border-gray-300').addClass('border-green-300');
            item.find('.check').removeClass('invisible');
            checkbox.prop('checked', true);
        } else {
            item.addClass('border-gray-300').removeClass('border-green-300');
            item.find('.check').addClass('invisible');
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
                $('#item-form')[0].reset();
                $('.item-card').each(function () {
                    $(this)
                        .addClass('border-gray-300')
                        .removeClass('border-green-300');
                    $(this).find('.check').addClass('invisible');
                    $(this).find('input[name="iditem[]"]').prop('checked', false);
                });
                $("#student_id").val('');
                startCamera();
            },
            error: function (xhr, status, error) {
                $("#student_id").val('');
                $('#loader').addClass('hidden').removeClass('flex');
                console.error("AJAX Error: ", error);
                console.error("Status: ", status);
                console.error("Response Text: ", xhr.responseText);
            }
        });
    }

    function closeErrorUser() {
        $("#error_user_modal").addClass("hidden").removeClass("flex");
        startCamera();
    }

    function openManualType() {
        $("#manual_type_modal").removeClass("hidden").addClass("flex");
        if (qrScanner) {
            qrScanner.stop()
                .then(() => console.log("Camera stopped because manual input modal opened."))
                .catch(err => console.warn("Error stopping camera:", err));
        }
    }

    function closeManualType() {
        $("#manual_type_modal").removeClass("flex").addClass("hidden");
        $("#student_id_manual").val('');
    }

    function submitID() {
        let student_id;
        if ($("#student_id").val() === "") {
            student_id = $("#student_id_manual").val();
            $("#student_id").val(student_id);
        } else {
            student_id = $("#student_id").val();
        }

        $.ajax({
            url: "logic/id_check.php",
            method: 'POST',
            data: {
                student_id: student_id
            },
            dataType: "json",
            success: function (response) {
                console.log(response.status);
                $('#loader').addClass('hidden').removeClass('flex');
                if (response.status == "success") {
                    $('#items').removeClass('hidden').addClass('flex');
                    console.log(response.status)
                } else if (response.status == "error") {
                    $("#error_user_modal").removeClass("hidden").addClass("flex");
                    console.log(response.status)
                    $("#student_id").val('');
                }
            },
            error: function (xhr, status, error) {
                $('#loader').addClass('hidden').removeClass('flex');
                $("#student_id").val('');
                console.error("AJAX Error: ", error);
                console.error("Status: ", status);
                console.error("Response Text: ", xhr.responseText);
                $("#error_user_modal").removeClass("hidden").addClass("flex");
            }
        });

        $("#manual_type_modal").removeClass("flex").addClass("hidden");
        $("#student_id_manual").val('');
    }

    function onScanSuccess(decodedText, decodedResult) {
        let data = decodedText;
        if (decodedText.includes("~")) {
            let qrData = decodedText.split("~");
            data = qrData[0].trim();
        }

        $("#student_id").val(data);

        $("#qr-reader")
            .addClass("border-green-500")
            .removeClass("border-transparent");
        setTimeout(() => {
            $("#qr-reader")
                .removeClass("border-green-500")
                .addClass("border-transparent");
        }, 500);

        // $("#items").removeClass("hidden").addClass("flex");
        submitID();
        qrScanner.stop()
            .then(() => console.log("Camera stopped because manual input modal opened."))
            .catch(err => console.warn("Error stopping camera:", err));
    }

    function onScanFailure(error) {
        // This runs when no QR is detected — you can safely leave it empty
        // console.warn("Scan failed:", error);
    }

    // 🔹 Handles camera selection and startup
    function startCamera() {
        Html5Qrcode.getCameras()
            .then(cameras => {
                if (cameras.length === 0) {
                    alert("No camera found.");
                    return;
                }

                const backCamera = cameras.find(cam =>
                    cam.label.toLowerCase().includes("back") ||
                    cam.label.toLowerCase().includes("rear")
                );
                const frontCamera = cameras.find(cam =>
                    cam.label.toLowerCase().includes("front") ||
                    cam.label.toLowerCase().includes("user")
                );

                const selectedCamera = backCamera || frontCamera || cameras[0];
                selectedCameraId = selectedCamera.id;

                return qrScanner.start(
                    selectedCameraId,
                    { fps: 10, qrbox: { width: 250, height: 250}, aspectRatio: 1.0 },
                    onScanSuccess,
                    onScanFailure
                );
            })
            .then(() => console.log("Camera started successfully."))
            .catch(err => alert("Error starting camera: " + err));
    }

    $(document).ready(function () {
        $('#header-title').text('QR Scan');

        $('#select-all').on('change', function () {
            let checked = $(this).prop('checked');
            $('.item-card').each(function () {
                if ($(this).data('stock') <= 0) return;

                let checkbox = $(this).find('input[name="iditem[]"]');
                if (checked) {
                    $(this).removeClass('border-gray-300').addClass('border-green-300');
                    $(this).find('.check').removeClass('invisible');
                    checkbox.prop('checked', true);
                } else {
                    $(this).addClass('border-gray-300').removeClass('border-green-300');
                    $(this).find('.check').addClass('invisible');
                    checkbox.prop('checked', false);
                }
            });
        });

        const qrReader = $("#qr-reader");

        qrScanner = new Html5Qrcode("qr-reader");

        startCamera();

        $("#cancel_btn").on("click", function () {
            startCamera();
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('#items-main').length && $(event.target).closest('#items').length) {
                $('#items').addClass('hidden').removeClass('flex');
                $('#item-form')[0].reset();
                $('.item-card').addClass('border-gray-300').removeClass('border-black');
                startCamera();
            }
        });
    });
</script>