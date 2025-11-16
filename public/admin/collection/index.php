<?php
include_once '../../includes/partial.php';
include_once '../../includes/connect-db.php';

if (session_status() === PHP_SESSION_NONE)
    session_start();

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
        END AS payment_type,
        is_void
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
    <?php include_once '../../includes/favicon.php'; ?>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .border-red-500 {
            border-color: #ef4444 !important;
            border-width: 2px !important;
        }

        /* Enhanced button hover effects */
        .print-btn {
            color: white;
            transition: all 0.3s ease;
        }

        .print-btn:hover:not(:disabled) {
            /* gray-700 */
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .print-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .void-btn {
            background-color: #000000;
            color: white;
            transition: all 0.3s ease;
        }

        .void-btn:hover:not(:disabled) {
            background-color: #dc2626 !important;
            /* red-700 */
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
        }

        .void-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.1);
        }

        /* Disabled state for both buttons */
        .print-btn:disabled,
        .void-btn:disabled {
            background-color: #9ca3af !important;
            /* gray-400 */
            color: #6b7280 !important;
            /* gray-500 */
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Focus states for accessibility */
        .print-btn:focus:not(:disabled),
        .void-btn:focus:not(:disabled) {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Voided receipt styles */
        .voided-receipt {
            filter: grayscale(100%);
            opacity: 0.7;
            position: relative;
        }

        .void-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border: 3px solid #dc2626;
            border-radius: 5px;
            z-index: 100;
        }
    </style>
</head>

<body class="w-full h-full bg-base-200">
    <div class="flex-1 p-4 md:p-8 overflow-y-auto w-full md:ml-0 pb-24">

        <div class="w-full flex justify-end">
            <button onclick="openDateSelect()"
                class="px-5 py-2 bg-primary text-white hover:cursor-pointer font-medium rounded-lg shadow-md hover:bg-primary/75 active:scale-95 transition-all duration-150">
                Batch Print <i class="fa-solid fa-print"></i>
            </button>
        </div>

        <form method="get" class="w-full">
            <div
                class="mt-5 bg-white flex flex-col md:flex-row items-center justify-between rounded-xl overflow-hidden shadow-sm p-4 mb-4 space-y-3 md:space-y-0 w-full  mx-auto">
                <div class="flex items-center w-full md:max-w-2xl border border-gray-300 rounded-lg">
                    <div class="px-3 py-2 text-white border rounded-l-lg bg-primary">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Search inventory..." class="px-2 w-full focus:outline-none py-0">
                </div>
                <div class="flex items-center border border-gray-300 rounded-lg w-full md:w-auto">
                    <button type="submit" class="text-white border bg-primary rounded-l-lg py-2 px-3">
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
                        <tr class="bg-base-200 text-primary">
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
                            <tr class="border-b <?= $purchase['is_void'] ? 'bg-gray-100 text-gray-500' : 'text-primary' ?>"
                                data-reference="<?= $purchase['ctrl_no'] ?>" data-date="<?= $purchase['date'] ?>"
                                data-quantity="<?= $purchase['quantity'] ?>" data-item="<?= $purchase['itemname'] ?>"
                                data-amount="<?= $purchase['value'] ?>" data-inventory="<?= $purchase['idinventory'] ?>"
                                data-officerName="<?= $purchase['officerName'] ?>"
                                data-mode="<?= $purchase['payment_type'] ?>" data-void="<?= $purchase['is_void'] ?>">

                                <td class="py-2 md:py-3 px-1 md:px-4 text-[9px] md:text-[12px] md:text-xs">
                                    <?= $purchase['ctrl_no'] ?>
                                    <?php if ($purchase['is_void']): ?>
                                        <span class="text-red-500 text-[8px] md:text-[10px]">(VOIDED)</span>
                                    <?php endif; ?>
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
                                <td class="py-2 md:py-3 px-2 md:px-4">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            class="print-btn bg-primary text-white px-2 md:px-4 py-1 rounded-full text-[10px] md:text-xs transition duration-300 <?= $purchase['is_void'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= $purchase['is_void'] ? 'disabled' : '' ?>>
                                            Print
                                        </button>
                                        <button
                                            class="void-btn text-white px-2 md:px-4 py-1 rounded-full text-[10px] md:text-xs transition duration-300 <?= $purchase['is_void'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= $purchase['is_void'] ? 'disabled' : '' ?>
                                            data-inventory-id="<?= $purchase['idinventory'] ?>"
                                            data-reference="<?= $purchase['ctrl_no'] ?>"
                                            data-student-name="<?= htmlspecialchars($purchase['username']) ?>">
                                            Void
                                        </button>
                                    </div>
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
                        class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400 transition duration-300">&lt;</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-3 py-2 rounded-lg transition duration-300 <?= $i == $page ? 'bg-black text-white' : 'bg-gray-300 text-black hover:bg-gray-400' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>"
                        class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400 transition duration-300">&gt;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Void Confirmation Modal -->
    <div id="voidModal" class="fixed inset-0 hidden" style="z-index: 100;">
        <!-- Blurred background overlay -->
        <div id="voidModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Modal container -->
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative w-full max-w-md mx-auto bg-white rounded-xl shadow-2xl z-50 border border-gray-200">
                <!-- Modal Content -->
                <div class="p-6 text-center pt-10">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>

                    <!-- Title -->
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Void Transaction</h3>

                    <!-- Message -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600" id="voidModalMessage">
                            Are you sure you want to void this transaction?
                        </p>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6 text-left">
                        <label for="adminPassword" class="block text-sm font-medium text-gray-700 mb-2">Admin
                            Password</label>
                        <input type="password" id="adminPassword"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                            placeholder="Enter your password">

                        <!-- Enhanced error display - MAKE SURE THIS IS EXACTLY LIKE THIS -->
                        <div id="passwordError" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg hidden">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-red-800 font-medium text-sm" id="passwordErrorText">Incorrect
                                        password</p>
                                    <p class="text-red-600 text-xs mt-1">Please check your password and try again.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Button -->
                    <div class="flex justify-center">
                        <button id="confirmVoid"
                            class="px-8 py-3  bg-black text-white rounded-lg hover:bg-red-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Confirm Void
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Date Select Modal -->
    <div id="dateModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xs p-6 m-6 relative">

            <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-black hover:cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-semibold text-gray-800 mb-4">Select Date Range</h2>

            <!-- Date Inputs -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" id="fromDate"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="endDate"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-black focus:outline-none">
                </div>
            </div>

            <!-- Go Button -->
            <div class="mt-6 flex justify-end">
                <button id="goBtn"
                    class="px-5 py-2 hover:cursor-pointer bg-black text-white rounded-lg font-medium hover:bg-gray-900 active:scale-95 transition-all duration-150">
                    Go
                </button>
            </div>
        </div>
    </div>


    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden"
        style="z-index: 9999;">
        <div
            class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center space-y-4 min-w-[200px] border border-gray-300">
            <div class="relative">
                <div class="w-12 h-12 border-4 border-gray-200 rounded-full"></div>
                <div
                    class="w-12 h-12 border-4 border-black border-t-transparent rounded-full animate-spin absolute top-0 left-0">
                </div>
            </div>
            <div class="text-center">
                <p class="text-lg font-semibold text-gray-800 mb-1">Processing</p>
                <p class="text-xs text-gray-500">This will just take a moment</p>
            </div>
        </div>
    </div>

    <?php
    include_once '../../includes/theme.php';
    include_once '../../includes/footer.php'; ?>

    <script>

        function showLoading() {
            $('#loadingOverlay').removeClass('hidden');
        }

        function hideLoading() {
            $('#loadingOverlay').addClass('hidden');
        }


        function openDateSelect() {
            $('#dateModal').removeClass('hidden').addClass('flex');
        }

        $('#closeModal').on('click', function () {
            $('#dateModal').addClass('hidden').removeClass('flex');
        });

        $('#goBtn').on('click', function () {
            const from = $('#fromDate').val();
            const to = $('#endDate').val();

            if (!from || !to) {
                return;
            }

            $('#dateModal').addClass('hidden').removeClass('flex');
        });

        $('#dateModal').on('click', function (e) {
            if ($(e.target).is('#dateModal')) {
                closeModal();
            }
        });



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
            console.log('jQuery loaded, version:', $.fn && $.fn.jquery);
            console.log('confirmVoid element:', $('#confirmVoid').length);

            $('#header-title').text('Collections');

            // Void Modal Handling
            const voidModal = $('#voidModal');
            const voidModalOverlay = $('#voidModalOverlay');
            const voidModalMessage = $('#voidModalMessage');
            const adminPassword = $('#adminPassword');
            const passwordError = $('#passwordError');
            const confirmVoid = $('#confirmVoid');

            let currentInventoryId = null;
            let currentVoidBtn = null;

            // Event listener for void buttons
            $('table').on('click', '.void-btn:not(:disabled)', function () {
                const inventoryId = $(this).data('inventory-id');
                const reference = $(this).data('reference');
                const studentName = $(this).data('student-name');

                currentInventoryId = inventoryId;
                currentVoidBtn = $(this);

                // Set modal content
                voidModalMessage.html(
                    `Are you sure you want to void transaction <strong>${reference}</strong> for <strong>${studentName}</strong>?<br><br>
                    <span class="text-red-600 font-semibold text-sm">This action cannot be undone.</span>`
                );

                // Reset form
                adminPassword.val('');
                passwordError.addClass('hidden');

                // Show modal
                voidModal.removeClass('hidden');

                // Focus on password input
                setTimeout(() => {
                    adminPassword.focus();
                }, 100);
            });

            // Confirm void action
            confirmVoid.on('click', function () {
                console.log('Confirm void button clicked!');
                const password = adminPassword.val().trim();

                if (!password) {
                    showPasswordError('Please enter your password');
                    adminPassword.focus();
                    return;
                }

                showLoading();

                // AJAX call with complete handler
                $.ajax({
                    url: 'logic/void_transaction.php',
                    type: 'POST',
                    data: {
                        inventory_id: currentInventoryId,
                        admin_password: password
                    },
                    success: function (response) {
                        console.log('Raw response:', response);

                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            console.log('Parsed result:', result);

                            // Accept both boolean true and string "true"
                            if (result.success === true || result.success === "true") {
                                updateRowToVoided(currentVoidBtn);
                                showSuccessMessage('Transaction voided successfully!');
                                voidModal.addClass('hidden');
                                resetModal();

                                // Reload page after short delay (optional)
                                setTimeout(function () {
                                    location.reload();
                                }, 1200);
                            } else {
                                showPasswordError(result.message || 'Operation failed. Please try again.');
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            showPasswordError('Server returned invalid response. Please try again.');
                        }
                    },

                    error: function (xhr, status, error) {
                        // AJAX ERROR (file not found, etc.)
                        console.error('AJAX error:', status, error);
                        showPasswordError('Cannot connect to server. Please check your connection.');
                    },
                    complete: function () {
                        // This runs ALWAYS - whether success or error
                        hideLoading();
                    }
                });
            });

            // NEW FUNCTION TO SHOW PASSWORD ERRORS PROPERLY
            function showPasswordError(message) {
                // Update the error text
                $('#passwordErrorText').text(message);
                // Show the error container
                $('#passwordError').removeClass('hidden');
                // Add red border to input
                $('#adminPassword').addClass('border-red-500');
                // Focus on password field
                $('#adminPassword').focus().val('');

                // Remove error when user starts typing again
                $('#adminPassword').off('input').on('input', function () {
                    $(this).removeClass('border-red-500');
                    $('#passwordError').addClass('hidden');
                });
            }

            // Update row to show voided state - SIMPLIFIED AND FIXED
            function updateRowToVoided($voidBtn) {
                console.log('Updating row to voided state...');

                if (!$voidBtn || !$voidBtn.length) {
                    console.error('Invalid void button provided');
                    return;
                }

                const $row = $voidBtn.closest('tr');
                console.log('Row found:', $row.length);

                if (!$row.length) {
                    console.error('Row not found for void button');
                    return;
                }

                // 1. Update the row background and text color
                $row.addClass('bg-gray-100 text-gray-500');

                // 2. Update the first cell to show VOIDED
                const $firstCell = $row.find('td:first');
                const currentText = $firstCell.text().replace('(VOIDED)', '').trim();
                $firstCell.html(currentText + ' <span class="text-red-500 text-[8px] md:text-[10px]">(VOIDED)</span>');

                // 3. Disable both buttons
                $row.find('.print-btn, .void-btn')
                    .addClass('opacity-50 cursor-not-allowed')
                    .prop('disabled', true);

                $row.find('.void-btn')
                    .text('Voiding')
                    .removeClass('bg-black hover:bg-red-700')
                    .addClass('bg-gray-400');

                // 5. Update data attribute
                $row.attr('data-void', '1');

                console.log('UI successfully updated for voided transaction');
            }

            // Success message function with toast notification
            function showSuccessMessage(message) {
                // Remove any existing toasts
                $('.success-toast').remove();

                // Create a toast notification
                const toast = $(`
                    <div class="success-toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>${message}</span>
                        </div>
                    </div>
                `);

                // Add to page with animation
                $('body').append(toast);
                toast.hide().fadeIn(300);

                // Remove after 3 seconds
                setTimeout(() => {
                    toast.fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 3000);
            }

            // Error message function with toast notification
            function showErrorMessage(message) {
                // Remove any existing toasts
                $('.error-toast').remove();

                // Create a toast notification
                const toast = $(`
                    <div class="error-toast fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>${message}</span>
                        </div>
                    </div>
                `);

                // Add to page with animation
                $('body').append(toast);
                toast.hide().fadeIn(300);

                // Remove after 3 seconds
                setTimeout(() => {
                    toast.fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 3000);
            }


            function resetModal() {
                adminPassword.val('');
                adminPassword.removeClass('border-red-500'); // Remove any red border
                passwordError.addClass('hidden');
                currentInventoryId = null;
                currentVoidBtn = null;
                hideLoading();

                // Remove any input event listeners to prevent memory leaks
                adminPassword.off('input');
            }

            // Close when clicking the overlay (outside modal)
            voidModalOverlay.on('click', function () {
                voidModal.addClass('hidden');
                resetModal();
            });

            // Also add escape key handler to close modal
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && !voidModal.hasClass('hidden')) {
                    voidModal.addClass('hidden');
                    resetModal();
                }
            });

            // Allow pressing Enter in password field to confirm
            adminPassword.on('keypress', function (e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    confirmVoid.trigger('click');
                }
            });

            // Event listener for table's print buttons
            $('table').on('click', '.print-btn:not(:disabled)', function () {
                var $row = $(this).closest('tr');

                var studentName = $row.find('td').eq(1).text().trim();
                var officerName = $row.data('officername');
                var date = $row.data('date');
                var item = $row.data('item');
                var amount = $row.data('amount');
                var reference = $row.data('reference');
                var mode = $row.data('mode');
                var isVoid = $row.data('void');

                printDynamicReceipt(studentName, officerName, date, item, amount, reference, mode, isVoid);
            });

            // Dynamic receipt print function - UPDATED TO INCLUDE VOID STATUS
            function printDynamicReceipt(studentName, officerName, date, item, amount, reference, mode, isVoid) {
                var printWindow = window.open('', '', 'width=300,height=400');
                printWindow.document.open();

                // Add voided styles and content
                const voidedStyle = isVoid ? `
                    .receipt {
                        filter: grayscale(100%);
                        opacity: 0.7;
                        position: relative;
                    }
                    .void-overlay {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) rotate(-45deg);
                        font-size: 24px;
                        font-weight: bold;
                        color: #dc2626;
                        background: rgba(255, 255, 255, 0.9);
                        padding: 10px 20px;
                        border: 3px solid #dc2626;
                        border-radius: 5px;
                        z-index: 100;
                    }
                ` : '';

                const voidedContent = isVoid ? `<div class="void-overlay">VOIDED</div>` : '';

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
                                    position: relative;
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
                                ${voidedStyle}
                            }
                        </style>
                    </head>
                    <body>
                        <div class="receipt">
                            ${voidedContent}
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

        $('#goBtn').on('click', function () {
            showLoading();
            const from = $('#fromDate').val();
            const to = $('#endDate').val();

            console.log(`js: ${from}, ${to}`);

            if (!from || !to) {
                alert('Please select both dates.');
                return;
            }

            // Hide modal when Go is clicked
            $('#dateModal').addClass('hidden');

            $.ajax({
                url: './logic/get_receipts.php',
                method: 'POST',
                data: { from, to },
                dataType: 'json',
                success: function (res) {
                    console.log('Response:', res);

                    if (res.status === 'success') {
                        if (res.data.length === 0) {
                            alert(`No receipts found from ${from} to ${to}`);
                            return;
                        }

                        console.log(`Found ${res.data.length} receipts from ${from} to ${to}`);
                        hideLoading();
                        batchPrint(res.data);
                    } else {
                        alert(res.message || 'Unknown error occurred.');
                    }
                },
                error: function (xhr, status, err) {
                    console.error('AJAX error:', err);
                    hideLoading();
                    alert('Error fetching receipts. Check console for details.');
                }
            });
        });


        function batchPrint(receipts) {
            const printDateTime = new Date().toLocaleString('en-PH', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });

            const totalReceipts = receipts.length;

            const printHTML = `
    <html>
    <head>
      <title>MoneyMo - Batch Print</title>
      <style>
        @media print {
          @page { margin: 8mm; }
          body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 8mm;
          }

          header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            font-size: 11px;
            font-weight: bold;
          }

          footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #333;
            border-top: 1px solid #000;
            padding-top: 4px;
          }

          footer::after {
            content: "Page " counter(page);
          }

          .page {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 6px;
            align-content: start;
          }

          .receipt {
            position: relative;
            border: 1px dashed #000;
            padding: 6px 5px;
            box-sizing: border-box;
            page-break-inside: avoid;
            overflow: hidden;
            min-height: 140px;
          }

          .title {
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            line-height: 1.2;
          }

          .info {
            font-size: 9px;
            margin: 1px 0;
          }

          .line {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
          }

          .total {
            font-weight: bold;
            margin-top: 3px;
            font-size: 10px;
          }

          .voided::before {
            content: "VOIDED";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            color: rgba(255, 0, 0, 0.15);
            font-size: 40px;
            font-weight: bold;
            white-space: nowrap;
            pointer-events: none;
          }

          hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 3px 0;
          }
        }
      </style>
    </head>
    <body>

      <div class="page">
        ${receipts.map(r => `
          <div class="receipt ${r.is_void ? 'voided' : ''}">
            <div class="title">👽 Association of Computer Scientists</div>
            <div class="title">PAYMENT RECEIPT</div>
            <div class="info">#${r.ctrl_no}</div>
            <hr>
            <div class="line"><strong>Date</strong><span>${r.date}</span></div>
            <div class="line"><strong>Name</strong><span>${r.username}</span></div>
            <div class="line"><strong>Item</strong><span>${r.itemname}</span></div>
            <div class="line"><strong>Qty</strong><span>${r.quantity}</span></div>
            <div class="line"><strong>Method</strong><span>${r.payment_type}</span></div>
            <div class="line"><strong>Assisted by</strong><span>${r.officerName}</span></div>
            <hr>
            <div class="line"><strong>Price</strong><span>₱${parseFloat(r.value).toFixed(2)}</span></div>
            <p class="total">Total: ₱${parseFloat(r.value).toFixed(2)}</p>
          </div>
        `).join('')}
      </div>

    </body>
    </html>
  `;

            // ✅ Silent print using hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(printHTML);
            doc.close();

            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            setTimeout(() => document.body.removeChild(iframe), 1000);
        }



    </script>
</body>

</html>
