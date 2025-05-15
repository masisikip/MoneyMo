<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="">
    <?php
    include_once '../../includes/partial.php';
    include_once '../../includes/connect-db.php';
    ?>

    <main class="min-h-screen p-4 flex justify-center items-center">
        <div class="bg-white w-full md:w-1/3 border rounded-xl p-6">
            <h2 class="text-2xl font-bold mb-4">Import Students</h2>

            <form id="importForm" class="space-y-4">
                <input type="file" name="student_file" accept=".csv" required class="border rounded-full cursor-pointer block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 
                          file:rounded-full file:border-0 file:text-sm file:font-semibold 
                          file:bg-black file:text-white hover:file:bg-gray-800">

                <button type="submit"
                    class="cursor-pointer  w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">
                    Preview Students
                </button>
            </form>

            <!-- Preview Area -->
            <div id="previewArea" class="mt-6"></div>
        </div>
    </main>

    <div id="message"
        class="mb-4 hidden w-96 absolute top-32 left-1/2 transform -translate-x-1/2 p-3 rounded text-center z-50">
    </div>

    <!-- Loading Overlay -->
    <div id="loaderOverlay" class="fixed inset-0 bg-gray-300/50 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-2">
            <span class="animate-spin h-5 w-5 border-4 border-blue-500 border-t-transparent rounded-full"></span>
            <p class="text-lg font-semibold">Processing...</p>
        </div>
    </div>
</body>


<script>
    $(function () {
        function showLoader() {
            $('#loaderOverlay').removeClass('hidden');
        }

        function hideLoader() {
            $('#loaderOverlay').addClass('hidden');
        }

        function showMessage(msg, type) {
            let bg = (type === 'success')
                ? 'bg-green-100 text-green-700 border border-green-400'
                : 'bg-red-100 text-red-700 border border-red-400';

            $('#message')
                .attr('class', `mb-4 w-96 fixed top-32 left-1/2 transform -translate-x-1/2 p-3 rounded text-center z-50 ${bg}`)
                .text(msg)
                .removeClass('hidden');

            setTimeout(() => {
                $('#message').addClass('hidden');
            }, 3000);
        }

        $('#importForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            showLoader();

            $.ajax({
                url: 'logic/preview_handler.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    $('#previewArea').html(res);
                },
                error: function () {
                    showMessage('Failed to preview.', 'error');
                },
                complete: function () {
                    hideLoader();
                }
            });
        });

        $(document).on('click', '#confirmInsert', function () {
            showLoader();

            var studentsJson = $('#studentsJson').val();

            $.ajax({
                url: 'logic/insert_handler.php',
                type: 'POST',
                data: { students_json: studentsJson },
                success: function (res) {
                    showMessage(res, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function () {
                    showMessage('Failed to insert', 'success');
                },
                complete: function () {
                    hideLoader();
                    $('#confirmInsert').prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed');
                }
            });
        });

    });

    $(document).ready(function () {
        $('#header-title').text('Import Data');
    })
</script>


</html>