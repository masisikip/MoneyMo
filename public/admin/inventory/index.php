<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Fix mobile view spacing */
        .mobile-card {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        /* Ensure table wraps properly */
        .table-container {
            overflow-x: auto;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php
    include_once '../../includes/partial.php';
    include_once '../../includes/connect-db.php';

    $limit = $_GET['limit'] ?? 10;
    $page = $_GET['page'] ?? 1;
    $offset = ($page - 1) * $limit;
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? '';

    // Build base WHERE conditions
    $whereClause = "WHERE 1 = 1";

    if (!empty($search)) {
        $whereClause .= " AND (item.name LIKE :search1 OR f_name LIKE :search2 OR l_name LIKE :search3)";
    }
    if ($filter === 'claimed') {
        $whereClause .= " AND is_received = 1";
    }
    if ($filter === 'unclaimed') {
        $whereClause .= " AND is_received = 0";
    }

    // ========================
// 1. Get total records first
// ========================
    $countQuery = "
    SELECT COUNT(*) 
    FROM inventory
    INNER JOIN item ON inventory.iditem = item.iditem
    INNER JOIN user ON inventory.iduser = user.iduser
    $whereClause
";

    $countStmt = $pdo->prepare($countQuery);

    if (!empty($search)) {
        $searchTerm = "%$search%";
        $countStmt->bindValue(':search1', $searchTerm);
        $countStmt->bindValue(':search2', $searchTerm);
        $countStmt->bindValue(':search3', $searchTerm);
    }

    $countStmt->execute();
    $total_records = $countStmt->fetchColumn(); // <<< Now we have the total
    
    $total_pages = ceil($total_records / $limit);

    // ========================
// 2. Then fetch paginated data
// ========================
    $query = "
    SELECT 
        idinventory,
        DATE(date) AS date,
        item.name AS itemname,
        CONCAT(f_name, ' ', l_name) AS username,
        is_received,
        CASE 
            WHEN is_received = 1 THEN 'Claimed'
            WHEN is_received = 0 THEN 'Claim'
            ELSE 'Unknown'
        END AS claim_button
    FROM inventory
    INNER JOIN item ON inventory.iditem = item.iditem
    INNER JOIN user ON inventory.iduser = user.iduser
    $whereClause
    ORDER BY date DESC
    LIMIT :limit OFFSET :offset
";

    $stmt = $pdo->prepare($query);

    if (!empty($search)) {
        $stmt->bindValue(':search1', $searchTerm);
        $stmt->bindValue(':search2', $searchTerm);
        $stmt->bindValue(':search3', $searchTerm);
    }

    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>


    <div class="flex flex-wrap justify-center mt-3 w-full px-4">
        <form method="get" class="w-full px-4">
            <div
                class="mt-5 bg-white flex flex-col md:flex-row items-center justify-between rounded-xl overflow-hidden shadow-sm p-4 mb-4 space-y-3 md:space-y-0 w-full max-w-5xl mx-auto">
                <div class="flex items-center w-full md:max-w-2xl">
                    <span class="pl-3 pr-2 text-gray-500">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Search inventory..."
                        class="p-2 w-full border border-gray-300 rounded-md focus:ring-2 focus:outline-none focus:ring-blue-400">
                </div>
                <div class="flex items-center space-x-2 md:space-x-4 w-full md:w-auto">
                    <select name="filter" class="p-2 border rounded-lg w-full md:w-auto">
                        <option value="" <?= (($_GET['filter'] ?? '') === '') ? 'selected' : '' ?>>All</option>
                        <option value="unclaimed" <?= (($_GET['filter'] ?? '') === 'unclaimed') ? 'selected' : '' ?>>
                            Unclaimed</option>
                        <option value="claimed" <?= (($_GET['filter'] ?? '') === 'claimed') ? 'selected' : '' ?>>Claimed
                        </option>
                    </select>

                    <button type="submit" class="text-gray-500 hover:text-black">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </div>
            </div>
        </form>


        <!-- Desktop View -->
        <div class="hidden md:block bg-white shadow rounded-lg p-6 mb-4 overflow-x-auto w-full max-w-5xl">
            <div class="table-container">
                <table class="w-full border-collapse overflow-hidden rounded-lg min-w-[600px]">
                    <thead class="bg-gray-200 text-gray-700 rounded-t-lg">
                        <tr>
                            <th class="py-2 px-4 text-left">Item Name <i class="fas fa-sort ml-2 text-gray-400"></i>
                            </th>
                            <th class="py-2 px-4 text-left">Purchased By <i class="fas fa-sort ml-2 text-gray-400"></i>
                            </th>
                            <th class="py-2 px-4 text-left">Date of Purchase <i
                                    class="fas fa-sort ml-2 text-gray-400"></i></th>
                            <th class="py-2 px-4 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr class="border-b">
                                <td class="py-3 px-4"><?= $purchase['itemname'] ?></td>
                                <td class="py-3 px-4"><?= $purchase['username'] ?></td>
                                <td class="py-3 px-4"><?= $purchase['date'] ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($purchase['is_received'] == 0): ?>
                                        <button
                                            onclick="confirmClaim(<?= $purchase['idinventory'] ?>, '<?= addslashes($purchase['itemname']) ?>', '<?= addslashes($purchase['username']) ?>')"
                                            class="bg-gray-950 cursor-pointer text-white px-4 py-1 rounded-full hover:bg-gray-800 min-w-[120px]">
                                            Claim
                                        </button>

                                    <?php else: ?>
                                        <span class="text-gray-500  text-center font-semibold min-w-[120px] inline-block">
                                            Claimed
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden space-y-4 w-full px-4">
            <?php foreach ($purchases as $purchase): ?>
                <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center mobile-card">
                    <div>
                        <p class="font-bold"><?= $purchase['itemname'] ?></p>
                        <p class="text-gray-600"><?= $purchase['username'] ?></p>
                        <p class="text-gray-500 text-sm"><?= $purchase['date'] ?></p>
                    </div>
                    <?php if ($purchase['is_received'] == 0): ?>
                        <button
                            onclick="confirmClaim(<?= $purchase['idinventory'] ?>, '<?= addslashes($purchase['itemname']) ?>', '<?= addslashes($purchase['username']) ?>')"
                            class="bg-gray-950 text-white cursor-pointer px-4 py-1 rounded-full hover:bg-gray-800  min-w-[120px]">
                            Claim
                        </button>

                    <?php else: ?>
                        <span class="text-gray-500 text-center font-semibold min-w-[120px] inline-block">
                            Claimed
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    $queryParams = $_GET;
    unset($queryParams['page']);
    $base_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams);
    ?>

    <!-- Pagination -->
    <div class="flex justify-center my-4 ">
        <div class="flex items-center space-x-2">
            <?php if ($page > 1): ?>
                <a href="<?= $base_url ?>&page=<?= $page - 1 ?>"
                    class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&lt;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="<?= $base_url ?>&page=<?= $i ?>"
                    class="px-3 py-2 rounded-lg <?= $i == $page ? 'bg-black text-white' : 'bg-gray-300 text-black hover:bg-gray-400' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?= $base_url ?>&page=<?= $page + 1 ?>"
                    class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&gt;</a>
            <?php endif; ?>
        </div>
    </div>




    <div id="confirmModal" class="fixed inset-0 bg-gray-300/50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <p id="confirmText" class="text-lg mb-4"></p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal()"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-400 bg-gray-300 rounded-lg">Cancel</button>
                <button id="confirmBtn"
                    class="px-4 py-2 cursor-pointer bg-gray-950 text-white rounded-lg hover:bg-gray-800 ">
                    Confirm
                </button>
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

</body>


<script>
    function confirmClaim(idinventory, itemname, username) {
        $("#confirmText").text(`Confirm that item '${itemname}' has been claimed by '${username}'?`);
        $("#confirmBtn").attr("onclick", `claimItem(${idinventory})`);
        $("#confirmModal").removeClass("hidden");
    }

    function closeModal() {
        $("#confirmModal").addClass("hidden");
    }

    function claimItem(idinventory) {
        closeModal();
        $("#loadingOverlay").removeClass("hidden");
        $.ajax({
            url: "logic/claim_item.php",
            type: "POST",
            data: { idinventory: idinventory },
            success: function (response) {
                $("#loadingOverlay").addClass("hidden");
                if (response.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error claiming item. Please try again.");
                }
            },
            error: function () {
                $("#loadingOverlay").addClass("hidden");
                alert("An error occurred while processing the request.");
            }
        });
    }



    $(document).ready(function () {
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

</html>