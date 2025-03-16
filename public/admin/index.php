<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="h-screen bg-gray-100">

    <?php
    include_once '../includes/partial.php';
    include_once '../includes/connect-db.php';
    ?>



    <div class="flex-1 p-4 md:p-8 overflow-y-auto w-full md:ml-0 pb-24">


        <div class="bg-white shadow rounded-lg p-4 md:p-6 overflow-x-auto">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-2 md:px-4 text-left text-[10px] md:text-xs cursor-pointer">
                                Student Name
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </th>
                            <th class="py-2 px-2 md:px-4 text-left text-[10px] md:text-xs cursor-pointer">
                                Item Category
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </th>
                            <th class="py-2 px-2 md:px-4 text-left text-[10px] md:text-xs cursor-pointer">
                                Price
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </th>
                            <th class="py-3 px-4 text-center text-[10px] md:text-xs">
                                Option
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 10; // Number of records per page
                        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // Get total count
                        $stmt = $pdo->query("SELECT COUNT(*) FROM inventory");
                        $total_records = $stmt->fetchColumn();
                        $total_pages = ceil($total_records / $limit);

                        // Fetch paginated data
                        $stmt = $pdo->prepare("
                            SELECT 
                            reference_no,
                            date(date) AS date,
                            CONCAT(f_name, ' ', l_name) AS username,
                            quantity,
                            name as itemname,
                            value,
                            idinventory,
                            CASE 
                            WHEN payment_type = 0 THEN 'Cash'
                                WHEN payment_type = 1 THEN 'Gcash'
                                ELSE 'unknown'
                            END AS 	payment_type
                        FROM inventory
                        INNER JOIN item on inventory.iditem = item.iditem
                        INNER JOIN user ON inventory.iduser = user.iduser
                        ORDER BY date desc   
                         LIMIT :limit OFFSET :offset 
                        ");

                        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        ?>
                        <?php foreach ($purchases as $purchase): ?>

                            <tr class="border-b" data-reference="<?= $purchase['reference_no'] ?>"
                                data-date="<?= $purchase['date'] ?>" data-quantity="<?= $purchase['quantity'] ?>"
                                data-item="<?= $purchase['itemname'] ?>" data-amount="<?= $purchase['value'] ?>"
                                data-inventory="<?= $purchase['idinventory'] ?>"
                                data-mode="<?= $purchase['payment_type'] ?>">

                                <td class="py-2 md:py-3 px-2 md:px-4 text-[10px] md:text-xs"><?= $purchase['username'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-2 md:px-4 text-[10px] md:text-xs"><?= $purchase['itemname'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-2 md:px-4 text-[10px] md:text-xs">₱ <?= $purchase['value'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-2 md:px-4 flex justify-center">
                                    <button
                                        class="bg-black text-white px-2 md:px-4 py-1 rounded-full text-[10px] md:text-xs hover:bg-gray-700 transition duration-300 cursor-pointer">Print</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center my-4 ">
            <div class="flex items-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>"
                        class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&lt;</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-3 py-2 rounded-lg <?= $i == $page ? 'bg-black text-white' : 'bg-gray-300 text-black hover:bg-gray-400' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>"
                        class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&gt;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');

            mobileMenuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('hidden');
                if (!sidebar.classList.contains('hidden')) {
                    sidebar.classList.add('fixed', 'inset-0', 'z-50');
                } else {
                    sidebar.classList.remove('fixed', 'inset-0', 'z-50');
                }
            });

            document.addEventListener('click', function (event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickInsideToggle = mobileMenuToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickInsideToggle && !sidebar.classList.contains('hidden') && window.innerWidth < 768) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('fixed', 'inset-0', 'z-50');
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('hidden', 'fixed', 'inset-0', 'z-50');
                } else {
                    sidebar.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>