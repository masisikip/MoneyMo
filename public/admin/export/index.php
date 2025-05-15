<?php
include_once '../../includes/partial.php';
include_once '../../includes/connect-db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Inventory</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="">
    <main class="min-h-screen p-4 flex justify-center items-center">
        <div class="bg-white w-full md:w-96 border rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4">Export Inventory Records</h2>

            <form id="exportForm" method="POST" action="logic/export_inventory.php" target="_blank" class="space-y-4">
                <div>
                    <label class="block font-semibold mb-1">Start Date</label>
                    <input type="date" id="startDate" name="start_date" class="border p-2 rounded w-full">
                </div>
                <div>
                    <label class="block font-semibold mb-1">End Date</label>
                    <input type="date" id="endDate" name="end_date" class="border p-2 rounded w-full">
                </div>
                <button type="submit" class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded w-full">
                    Export to CSV
                </button>
            </form>

        </div>
    </main>
    <?php include_once '../../includes/footer.php'; ?>

    <script>
        $('#exportForm').on('submit', function (e) {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();

            if (!startDate || !endDate) {
                e.preventDefault();
                return;
            }

            if (startDate > endDate) {
                e.preventDefault();
                return;
            }

        });

    </script>
</body>

</html>