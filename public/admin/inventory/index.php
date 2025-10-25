<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMo - Inventory</title>
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

    $whereClause = "WHERE inventory.is_void = 0"; // Only show non-voided transactions

    if (!empty($search)) {
        $whereClause .= " AND (item.name LIKE :search1 OR f_name LIKE :search2 OR l_name LIKE :search3)";
    }
    if ($filter === 'claimed') {
        $whereClause .= " AND is_received = 1";
    }
    if ($filter === 'unclaimed') {
        $whereClause .= " AND is_received = 0";
    }

    $countQuery = "
    SELECT COUNT(DISTINCT user.iduser)
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
    $total_records = $countStmt->fetchColumn();

    $total_pages = ceil($total_records / $limit);

    $userIdsQuery = "
        SELECT DISTINCT user.iduser, CONCAT(f_name, ' ', l_name) AS username
        FROM inventory
        INNER JOIN item ON inventory.iditem = item.iditem
        INNER JOIN user ON inventory.iduser = user.iduser
        $whereClause
        ORDER BY username ASC
        LIMIT :limit OFFSET :offset
        ";

    $userIdsStmt = $pdo->prepare($userIdsQuery);

    if (!empty($search)) {
        $userIdsStmt->bindValue(':search1', $searchTerm);
        $userIdsStmt->bindValue(':search2', $searchTerm);
        $userIdsStmt->bindValue(':search3', $searchTerm);
    }

    $userIdsStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $userIdsStmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $userIdsStmt->execute();
    $userRows = $userIdsStmt->fetchAll(PDO::FETCH_ASSOC);

    $userIds = array_column($userRows, 'iduser');


    if (!empty($userIds)) {
        // Generate named placeholders like :id1, :id2, :id3
        $placeholders = [];
        foreach ($userIds as $index => $uid) {
            $placeholders[] = ':id' . $index;
        }
        $placeholdersString = implode(',', $placeholders);

        $query = "
        SELECT 
            idinventory,
            DATE(date) AS date,
            DATE(received_at) AS claimdate,
            item.name AS itemname,
            CONCAT(f_name, ' ', l_name) AS username,
            is_received,
            inventory.is_void,
            CASE 
                WHEN is_received = 1 THEN 'Claimed'
                WHEN is_received = 0 THEN 'Claim'
                ELSE 'Unknown'
            END AS claim_button
        FROM inventory
        INNER JOIN item ON inventory.iditem = item.iditem
        INNER JOIN user ON inventory.iduser = user.iduser
        $whereClause AND user.iduser IN ($placeholdersString)
        ORDER BY date DESC
        ";

        $stmt = $pdo->prepare($query);

        // Bind search terms
        if (!empty($search)) {
            $stmt->bindValue(':search1', $searchTerm);
            $stmt->bindValue(':search2', $searchTerm);
            $stmt->bindValue(':search3', $searchTerm);
        }

        // Bind user IDs
        foreach ($userIds as $index => $uid) {
            $stmt->bindValue(':id' . $index, $uid, PDO::PARAM_INT);
        }

        $stmt->execute();
        $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $purchases = [];
    }



    // Group purchases by user
    $groupedPurchases = [];
    foreach ($purchases as $purchase) {
        $groupedPurchases[$purchase['username']][] = $purchase;
    }

    ?>


    <div class="flex flex-wrap justify-center mt-3 w-full px-4">
        <form method="get" class="w-full px-4">
            <div
                class="mt-5 bg-white flex flex-col md:flex-row items-center justify-between rounded-xl overflow-hidden shadow-sm p-4 mb-4 space-y-3 md:space-y-0 w-full max-w-5xl mx-auto">
                <div class="flex items-center w-full md:max-w-2xl border border-gray-300 rounded-lg">
                    <div class="px-3 py-1 text-white border rounded-l-lg bg-zinc-700">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Search inventory..." class="px-2 w-full focus:outline-none py-0">
                </div>
                <div class="flex items-center border border-gray-300 rounded-lg w-full md:w-auto">
                    <button type="submit" class="text-white border bg-zinc-700 rounded-l-lg py-1 px-3">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <select name="filter" class="px-2 focus:outline-none w-full md:w-auto">
                        <option value="" <?= (($_GET['filter'] ?? '') === '') ? 'selected' : '' ?>>All</option>
                        <option value="unclaimed" <?= (($_GET['filter'] ?? '') === 'unclaimed') ? 'selected' : '' ?>>
                            Unclaimed</option>
                        <option value="claimed" <?= (($_GET['filter'] ?? '') === 'claimed') ? 'selected' : '' ?>>Claimed
                        </option>
                    </select>
                </div>
            </div>
        </form>


        <!-- Desktop View -->
        <div class="hidden md:block bg-white shadow rounded-lg p-6 mb-4 overflow-x-auto w-full max-w-5xl">
            <div class="table-container">

                <?php foreach ($groupedPurchases as $username => $items): ?>
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <!-- User Header (clickable) -->
                        <button onclick="toggleUserItems(this)"
                            class="w-full text-left bg-gray-200 px-4 py-2 font-bold flex justify-between items-center">
                            <?= htmlspecialchars($username) ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <!-- Items Table (collapsed initially) -->
                        <div class="user-items hidden">
                            <table class="w-full border-collapse min-w-[600px]">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Item Name</th>
                                        <th class="py-2 px-4 text-left">Date of Purchase</th>
                                        <th class="py-2 px-4 text-left">Claim Date</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $purchase): ?>
                                        <tr class="border-b">
                                            <td class="py-3 px-4"><?= htmlspecialchars($purchase['itemname']) ?></td>
                                            <td class="py-3 px-4"><?= htmlspecialchars($purchase['date']) ?></td>
                                            <td class="py-3 px-4"><?= htmlspecialchars($purchase['claimdate']) ?></td>
                                            <td class="py-3 px-4">
                                                <?php if ($purchase['is_received'] == 0): ?>
                                                    <button
                                                        onclick="confirmClaim(<?= $purchase['idinventory'] ?>, '<?= addslashes($purchase['itemname']) ?>', '<?= addslashes($purchase['username']) ?>', this)"
                                                        class="bg-gray-950 cursor-pointer text-white px-4 py-1 rounded-full hover:bg-gray-800 min-w-[120px]">
                                                        Claim
                                                    </button>
                                                <?php else: ?>
                                                    <span
                                                        class="text-gray-500 font-semibold min-w-[120px] text-center inline-block">
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
                <?php endforeach; ?>

            </div>
        </div>


        <!-- Mobile View -->
        <div class="md:hidden space-y-4 w-full px-4">
            <?php foreach ($groupedPurchases as $username => $items): ?>
                <div class="border rounded-lg overflow-hidden">
                    <!-- User Header -->
                    <button onclick="toggleUserItems(this)"
                        class="w-full text-left bg-gray-200 px-4 py-2 font-bold flex justify-between items-center">
                        <?= htmlspecialchars($username) ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <!-- Items List -->
                    <div class="user-items hidden space-y-2 p-2">
                        <?php foreach ($items as $purchase): ?>
                            <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
                                <div>
                                    <p class="font-bold"><?= htmlspecialchars($purchase['itemname']) ?></p>
                                    <p class="text-gray-500 text-sm">Purchased on <?= htmlspecialchars($purchase['date']) ?></p>
                                    <?php if ($purchase['claimdate']) { ?>
                                        <p class="text-gray-500 text-sm">Claimed on <?= htmlspecialchars($purchase['claimdate']) ?>
                                        </p>
                                    <?php } ?>
                                </div>
                                <?php if ($purchase['is_received'] == 0): ?>
                                    <button
                                        onclick="confirmClaim(<?= $purchase['idinventory'] ?>, '<?= addslashes($purchase['itemname']) ?>', '<?= addslashes($purchase['username']) ?>', this)"
                                        class="bg-gray-950 text-white cursor-pointer px-4 py-1 rounded-full hover:bg-gray-800 min-w-[80px]">
                                        Claim
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-500 text-center font-semibold min-w-[80px] inline-block">Claimed
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
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

    <?php include_once '../../includes/footer.php'; ?>

</body>


<script>
    function confirmClaim(idinventory, itemname, username, buttonElement) {
        $("#confirmText").text(`Confirm that item '${itemname}' has been claimed by '${username}'?`);
        $("#confirmBtn").off("click").on("click", function () {
            claimItem(idinventory, buttonElement);
        });

        $("#confirmModal").removeClass("hidden");
    }


    function closeModal() {
        $("#confirmModal").addClass("hidden");
    }

    function claimItem(idinventory, buttonElement) {
        closeModal();
        $("#loadingOverlay").removeClass("hidden");
        $.ajax({
            url: "logic/claim_item.php",
            type: "POST",
            data: { idinventory: idinventory },
            success: function (response) {
                $("#loadingOverlay").addClass("hidden");
                if (response.trim() === "success") {
                    // Disable the button and change its text
                    $(buttonElement)
                        .replaceWith('<span class="text-gray-500 text-center font-semibold min-w-[120px] inline-block">Claimed</span>');
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
        $('#header-title').text('Inventory');

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


    function toggleUserItems(button) {
        const itemsDiv = button.nextElementSibling;
        itemsDiv.classList.toggle('hidden');

        // Toggle arrow icon
        const icon = button.querySelector('i');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }
</script>

</html>