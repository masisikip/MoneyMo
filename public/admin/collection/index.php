<?php
include_once '../../includes/partial.php';
include_once '../../includes/connect-db.php';


$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$whereClause = "WHERE 1 = 1";

if (!empty($search)) {
    $whereClause .= " AND (item.name LIKE :search1 OR u1.f_name LIKE :search2 OR u1.l_name LIKE :search3)";
}

if (!empty($filter)) {
    $whereClause .= " AND item.iditem = :filter";
}


$countQuery = "
    SELECT COUNT(*)
    FROM inventory
    INNER JOIN item ON inventory.iditem = item.iditem
    INNER JOIN user u1 ON inventory.iduser = u1.iduser
    $whereClause
    ";


$countStmt = $pdo->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $countStmt->bindValue(':search1', $searchTerm);
    $countStmt->bindValue(':search2', $searchTerm);
    $countStmt->bindValue(':search3', $searchTerm);
}


if (!empty($filter)) {
    $countStmt->bindValue(':filter', $filter, PDO::PARAM_INT);
}


$countStmt->execute();
$total_records = $countStmt->fetchColumn();

$total_pages = ceil($total_records / $limit);


$stmt = $pdo->prepare("
    SELECT 
        ctrl_no,
        date(date) AS date,
        CONCAT(u1.f_name, ' ', u1.l_name) AS username,
        quantity,
        name as itemname,
        inventory.value,
        idinventory,
        CONCAT(u2.f_name, ' ', u2.l_name) AS officerName,
        CASE 
        WHEN payment_type = 0 THEN 'Cash'
            WHEN payment_type = 1 THEN 'Gcash'
            ELSE 'unknown'
        END AS 	payment_type
    FROM inventory
    INNER JOIN item on inventory.iditem = item.iditem
    INNER JOIN user u1 ON inventory.iduser = u1.iduser
    INNER JOIN user u2 ON inventory.idofficer = u2.iduser
    $whereClause
    ORDER BY date desc, ctrl_no desc   
    LIMIT :limit OFFSET :offset 
    ");


if (!empty($search)) {
    $stmt->bindValue(':search1', $searchTerm);
    $stmt->bindValue(':search2', $searchTerm);
    $stmt->bindValue(':search3', $searchTerm);
}

if (!empty($filter)) {
    $stmt->bindValue(':filter', $filter, PDO::PARAM_INT);
}


$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);



$stmt2 = $pdo->query("
    SELECT 
        iditem,
        name
    FROM item
");
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMo - Collections</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="w-full h-full bg-gray-100">
    <div class="flex-1 p-4 md:p-8 overflow-y-auto w-full md:ml-0 pb-24">
        <form method="get" class="w-full">
            <div
                class="mt-5 bg-white flex flex-col md:flex-row items-center justify-between rounded-xl overflow-hidden shadow-sm p-4 mb-4 space-y-3 md:space-y-0 w-full  mx-auto">
                <div class="flex items-center w-full md:max-w-2xl border border-gray-300 rounded-lg">
                    <div class="px-3 py-2 text-white border rounded-l-lg bg-zinc-700">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Search inventory..." class="px-2 w-full focus:outline-none py-0">
                </div>
                <div class="flex items-center border border-gray-300 rounded-lg w-full md:w-auto">
                    <button type="submit" class="text-white border bg-zinc-700 rounded-l-lg py-2 px-3">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <select name="filter" class="px-2 focus:outline-none w-full md:w-auto">

                        <option value="" <?= (($_GET['filter'] ?? '') === '') ? 'selected' : '' ?>>All</option>

                        <?php foreach ($items as $item) { ?>
                            <option value="<?= $item['iditem'] ?>" <?= (isset($_GET['filter']) && $_GET['filter'] == $item['iditem']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['name']) ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded-lg p-4 md:p-6 overflow-x-auto">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Ctrl no
                            </th>
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Student Name
                            </th>
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Date
                            </th>
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Item Category
                            </th>
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Price
                            </th>
                            <th class="py-2 px-1 md:px-4 text-left text-[9px] md:text-[12px] md:text-xs cursor-pointer">
                                Assisted by
                            </th>
                            <th class="py-3 px-4 text-center text-[9px] md:text-[12px] md:text-xs">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>

                            <tr class="border-b" data-reference="<?= $purchase['ctrl_no'] ?>"
                                data-date="<?= $purchase['date'] ?>" data-quantity="<?= $purchase['quantity'] ?>"
                                data-item="<?= $purchase['itemname'] ?>" data-amount="<?= $purchase['value'] ?>"
                                data-inventory="<?= $purchase['idinventory'] ?>"
                                data-officerName="<?= $purchase['officerName'] ?>"
                                data-mode="<?= $purchase['payment_type'] ?>">

                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['ctrl_no'] ?>
                                </td>

                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['username'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['date'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['itemname'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">₱
                                    <?= $purchase['value'] ?>
                                </td>
                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['officerName'] ?>
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


    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-300/50 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-2">
            <span class="animate-spin h-5 w-5 border-4 border-blue-500 border-t-transparent rounded-full"></span>
            <p class="text-lg font-semibold">Processing...</p>
        </div>
    </div>






    <?php include_once '../../includes/footer.php'; ?>

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



        $(document).ready(function () {
            $('#header-title').text('Collections');

            // Event listener for table's print buttons
            $('table').on('click', 'button', function () {
                var $row = $(this).closest('tr');

                var studentName = $row.find('td').eq(0).text().trim();
                var officerName = $row.data('officername');
                var date = $row.data('date');
                var item = $row.data('item');
                var amount = $row.data('amount');
                var reference = $row.data('reference');
                var mode = $row.data('mode');

                printDynamicReceipt(studentName, officerName, date, item, amount, reference, mode);
            });

            // Dynamic receipt print function
            function printDynamicReceipt(studentName, officerName, date, item, amount, reference, mode) {
                var printWindow = window.open('', '', 'width=300,height=400');
                printWindow.document.open();
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>MoneyMo - Print</title>
                        <style>
                            @media print {
                                @page { margin: 0; }
                                body { 
                                    margin: 0; 
                                    font-family: 'Courier New', monospace; 
                                    text-align: center;
                                    width: 45mm; 
                                }
                                .receipt {
                                    padding: 3px;
                                }
                                .address {
                                    font-size: 10px;
                                    margin-bottom: 4px;
                                }
                                hr {
                                    border: none;
                                    border-top: 1px dashed black;
                                    margin: 4px 0;
                                }
                                .title {
                                    font-size: 12px;
                                    font-weight: bold;
                                    margin: 2px 0;
                                }
                                .info {
                                    font-size: 10px;
                                    margin: 1px 0;
                                }
                                .item, .line {
                                    display: flex;
                                    justify-content: space-between;
                                    font-size: 10px;
                                }
                                .total {
                                    font-size: 11px;
                                    font-weight: bold;
                                    margin: 4px 0;
                                }
                                .spacer {
                                    font-size: 8px;
                                    margin: 0;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="receipt">
                            <p class="spacer">...</p>
                            <br>
                            <p class="title">👽</p>
                            <p class="info">Association of</p>
                            <p class="info">Computer Scientists</p>
                            <p class="title">PAYMENT RECEIPT</p>
                            <p class="info">${reference}</p>
                            <hr>

                            <div class="line"><strong>Date</strong><span>${new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' })}</span></div>
                            <div class="line"><strong>Name</strong><span>${studentName}</span></div>
                            <div class="line"><strong>Item</strong><span>${item}</span></div>
                            <div class="line"><strong>Qty</strong><span>1</span></div>
                            <div class="line"><strong>Method</strong><span>${mode}</span></div>
                            <div class="line"><strong>Astd by</strong><span>${officerName}</span></div>

                            <hr>

                            <div class="line"><strong>Price</strong><span>₱${parseFloat(amount).toFixed(2)}</span></div>
                            <hr>

                            <p class="total">Total: ₱${parseFloat(amount).toFixed(2)}</p>

                            <hr>
                    
                            <p class="info">This is a customer's copy.</p>
                            <p class="info">Thank You!</p>
                            <br>
                                <br>
                            <p class="spacer">...</p>
                        </div>
                    </body>
                    </html>
                `);

                printWindow.document.close();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }






            function showLoading() {
                $('#loadingOverlay').removeClass('hidden');
            }

            $('select[name="filter"]').on('change', function () {
                showLoading();
                $(this).closest('form').submit();
            });

            let typingTimer;
            const doneTypingInterval = 300;
            const $searchInput = $('input[name="search"]');

            $searchInput.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    showLoading();
                    $(this).closest('form').submit();
                }, doneTypingInterval);
            });

            $searchInput.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            $searchInput.on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    showLoading();
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
</body>

</html>