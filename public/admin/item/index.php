<?php
include_once '../../includes/connect-db.php';

$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$whereClause = "WHERE 1 = 1";

if (!empty($search)) {
    $whereClause .= " AND (item.name LIKE :search)";
}

// Updated filter logic
if ($filter === 'in_stock') {
    $whereClause .= " AND stock >= 10";
} elseif ($filter === 'low_stock') {
    $whereClause .= " AND stock > 0 AND stock < 10";
} elseif ($filter === 'out_of_stock') {
    $whereClause .= " AND stock = 0";
}

// Count query
$countQuery = "
    SELECT COUNT(*)
    FROM item
    $whereClause
";

$countStmt = $pdo->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $countStmt->bindValue(':search', $searchTerm);
}

$countStmt->execute();
$total_records = $countStmt->fetchColumn();

$total_pages = ceil($total_records / $limit);

// Main data query
$query = "
    SELECT *
    FROM item
    $whereClause
    ORDER BY name
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $stmt->bindValue(':search', $searchTerm);
}

$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/styles.css">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .icon-button {
            transition: background-color 0.3s, color 0.3s;
        }

        .icon-button:hover {
            background-color: #000000;
            color: #ffffff;
        }

        .icon-button:active {
            background-color: #ffffff;
            color: #000000;
        }

        .price-container h3,
        .item-name,
        .item-stock {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="flex flex-col h-screen bg-gray-100">
    <?php include_once '../../includes/partial.php' ?>


    <div class="w-full flex justify-end mt-5 mr-20 pr-10">

        <button class="text-white  bg-black px-4 py-2 rounded flex items-center add-item-button"
            onclick="openAddModal()">
            <i class="fas fa-plus mr-2"></i> <span>Add Item</span>
        </button>
    </div>


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
                    <option value="in_stock" <?= (($_GET['filter'] ?? '') === 'in_stock') ? 'selected' : '' ?>>In Stock
                    </option>
                    <option value="low_stock" <?= (($_GET['filter'] ?? '') === 'low_stock') ? 'selected' : '' ?>>Low
                        Stock
                    </option>
                    <option value="out_of_stock" <?= (($_GET['filter'] ?? '') === 'out_of_stock') ? 'selected' : '' ?>>
                        Out of Stock</option>
                </select>

            </div>
        </div>
    </form>

    <main class="justify-center mt-3 w-full px-4">

        <div id="content" class="flex-1 px-8 py-4 content">
            <!-- Item Grid -->
            <div
                class="grid justify-center w-full place-items-center grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9 lg:justify-items-center xl:justify-items-center">
                <?php foreach ($items as $item): ?>
                    <div id="<?= $item['iditem'] ?>-item" data-iditem="<?= $item['iditem'] ?>"
                        data-code="<?= $item['code'] ?>" data-name="<?= $item['name'] ?>" data-value="<?= $item['value'] ?>"
                        data-stock="<?= $item['stock'] ?>"
                        data-image="data:image/jpeg;base64,<?= base64_encode($item['image']); ?>"
                        class="cursor-pointer flex flex-col justify-center items-center relative bg-white border border-gray-300 rounded-lg hover:scale-105 transition-transform duration-300 ease-in-out w-[15rem] h-[22rem]">

                        <!-- Item Image -->
                        <?php if (!empty($item['image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" alt="Item Image"
                                class="object-cover h-full rounded-lg item-image">
                        <?php endif; ?>

                        <!-- Item Title Overlay -->
                        <div class="absolute top-0 left-0 right-0 flex items-center justify-between h-16 px-3 text-black">
                            <div class="flex rounded-full bg-[#ffffffa8] border border-zinc-700/60">
                                <div onclick="openUpdateModal(<?= $item['iditem'] ?>)"
                                    class="flex justify-center w-1/2 h-full px-2 py-1 text-gray-600 border-r rounded-l-full cursor-pointer border-r-gray-600 icon-button">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </div>
                                <div onclick="openDeleteModal(<?= $item['iditem'] ?>)"
                                    class="flex justify-center w-1/2 h-full px-2 py-1 text-gray-600 rounded-r-full cursor-pointer icon-button">
                                    <i class="fa-solid fa-trash"></i>
                                </div>
                            </div>
                            <div
                                class="price-container text-white bg-gray-800/80 rounded-full shadow-lg px-2 py-1 min-w-14">
                                <h3 class="text-l text-center">
                                    ₱ <?php echo htmlspecialchars($item['value']); ?>
                                </h3>
                            </div>
                        </div>

                        <!-- Item Name -->
                        <div
                            class="absolute bottom-0 left-0 right-0 flex flex-col items-center justify-center h-28 px-3 text-white rounded-b-lg bg-gradient-to-t from-zinc-800/90 via-zinc-800/30 to-transparent">
                            <h3 class="text-lg font-bold item-name text-center">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </h3>
                            <p class="text-base item-stock">
                                In Stock &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                                <?php echo htmlspecialchars($item['stock']); ?>
                                Units
                            </p>
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




    </main>

    <!-- Add Item Modal -->
    <div id="addItemModal"
        class="fixed w-full h-full items-center justify-center bg-gray-600/40 backdrop-blur-lg hidden">
        <div id="add-main" class="bg-white p-6 rounded-lg shadow-lg modal-content">
            <h2 class="text-xl font-bold mb-4">Add New Item</h2>
            <form id="addItemForm" action="logic/item_create.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                </div>
                <div class="mb-4">
                    <label for="value" class="block text-gray-700">Value <span class="text-red-500">*</span></label>
                    <input type="number" id="value" name="value" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700">Stock <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Image <span class="text-red-500">*</span></label>
                    <input type="file" id="image" name="image"
                        class="w-full border border-gray-300 rounded mt-1 file:p-2 file:bg-gray-700 file:text-white"
                        required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded mr-2"
                        onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Item Modal -->
    <div id="updateItemModal"
        class="fixed w-full h-full items-center justify-center bg-gray-600/40 backdrop-blur-lg hidden">
        <div id="update-main" class="w-10/12 bg-white p-6 rounded-lg shadow-lg h-4/5 md:w-1/4 overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Update Item</h2>
            <form id="updateItemForm" action="logic/item_update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="update_iditem" name="iditem">
                <!-- Image Preview -->
                <div class="mb-4 flex flex-col items-center">
                    <div class="relative w-64 h-80 rounded-lg shadow-lg shadow-zinc-700/50">
                        <img id="preview" src="" alt="Item Image" class="h-full object-cover rounded-lg">
                        <label for="update_image"
                            class="absolute text-sm rounded px-2 py-1 bottom-1 right-1 bg-zinc-600 hover:bg-zinc-700 text-white cursor-pointer"><i
                                class="fa-solid fa-upload"></i></label>
                    </div>
                    <input type="file" id="update_image" name="image" class="hidden">
                </div>

                <div class="mb-4">
                    <p class="block text-gray-700">Code</p>
                    <div class="w-full p-2 border border-gray-300 rounded mt-1 text-gray-500"><span id="update_code">
                            <innerHTMLspan>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="update_name" class="block text-gray-700">Name</label>
                    <input type="text" id="update_name" name="name"
                        class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="update_value" class="block text-gray-700">Value</label>
                    <input type="number" id="update_value" name="value"
                        class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="update_stock" class="block text-gray-700">Stock</label>
                    <input type="number" id="update_stock" name="stock"
                        class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded mr-2"
                        onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteItemModal"
        class="fixed w-full h-full items-center justify-center bg-gray-600/40 backdrop-blur-lg hidden">
        <div id="delete-main" class="w-10/12 md:w-1/4 bg-white rounded-lg flex flex-col px-4 py-2">
            <div class="py-2 font-semibold text-xl w-full border-b">Delete Item</div>
            <div class="w-full my-2 text-lg">
                <p>Are you sure to delete item <span id="delete-item" class="font-semibold"></span>?</p>
            </div>
            <input type="hidden" id="delete-iditem">

            <div class="flex gap-3 justify-center w-full mt-6 my-2">
                <button type="button" class="w-20 py-1 rounded bg-gray-700 text-white hover:bg-gray-800 cursor-pointer"
                    onclick="closeDeleteModal()">Cancel</button>
                <button class="w-20 py-1 rounded bg-red-500 hover:bg-red-600 text-white cursor-pointer"
                    onclick="confirmDelete()">Confirm</button>
            </div>
        </div>
    </div>

    <!--Success Message -->
    <div id="success"
        class="fixed top-0 w-full h-full bg-gray-500/50 backdrop-blur-xs justify-center items-center hidden">
        <div id="success-main" class="w-80 p-4 bg-white rounded-xl flex flex-col">
            <div class="w-full h-fit pb-4 flex justify-center">
                <i class="fa-solid fa-circle-check text-7xl text-green-500"></i>
            </div>
            <div class="mb-2 text-2xl font-semibold text-center">Success!</div>
            <div id="success-message" class="w-full text-center"></div>

            <div class="w-full mt-5 flex justify-center">
                <button onclick="hideSuccessMessage()"
                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 cursor-pointer">Okay</button>
            </div>
        </div>
    </div>

    <!--Error Message -->
    <div id="error"
        class="fixed top-0 w-full h-full bg-gray-500/50 backdrop-blur-xs justify-center items-center hidden">
        <div id="error-main" class="w-80 p-4 bg-white rounded-xl flex flex-col">
            <div class="w-full h-fit pb-4 flex justify-center">
                <i class="fa-solid fa-circle-check text-7xl text-red-500"></i>
            </div>
            <div class="mb-2 text-2xl font-semibold text-center">Error!</div>
            <div id="error-message" class="w-full text-center"></div>

            <div class="w-full mt-5 flex justify-center">
                <button onclick="hideErrorMessage()"
                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 cursor-pointer">Okay</button>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader"
        class="fixed top-0 w-full h-full bg-gray-500/50 backdrop-blur-xs justify-center items-center hidden">
        <div class="w-16 h-16 border-6 border-t-gray-800 border-gray-300 rounded-full animate-spin"></div>
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
    function openDeleteModal(iditem) {
        let id = $('#' + iditem + '-item');
        let name = id.data('name');
        $('#deleteItemModal').removeClass('hidden').addClass('flex');
        $('#delete-item').text(name);
        $('#delete-iditem').val(iditem);
        $('body').addClass('overflow-y-hidden');
    }

    function closeDeleteModal(iditem) {
        $('#deleteItemModal').addClass('hidden').removeClass('flex');
        $('body').removeClass('overflow-y-hidden');
    }

    function confirmDelete() {
        let iditem = $('#delete-iditem').val();
        $('#loader').removeClass('hidden').addClass('flex');
        $.ajax({
            url: "logic/item_delete.php",
            method: 'POST',
            data: { iditem: iditem },
            dataType: 'json',
            // processData: false,
            // contentType: false,
            success: function (response) {
                if (response['status'] == 'success') {
                    localStorage.setItem("deleteSuccess", "true");
                    localStorage.setItem('message', response['message']);
                    location.reload();
                } else {
                    localStorage.setItem("deleteError", "true");
                    localStorage.setItem('message', response['message']);
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                $('#error').addClass('flex').removeClass('hidden');
                $('#error-message').text(response.message);
                location.reload();
                localStorage.setItem("deleteError", "true");
            }
        })
    }

    function openAddModal() {
        $('#addItemModal').removeClass('hidden').addClass('flex');
        $('body').addClass('overflow-y-hidden');
    }

    function closeAddModal() {
        $('#addItemModal').addClass('hidden').removeClass('flex');
        $('#addItemForm')[0].reset();
        $('body').removeClass('overflow-y-hidden');
    }

    function openUpdateModal(iditem) {
        const item = document.getElementById(`${iditem}-item`);
        document.getElementById('update_iditem').value = item.dataset.iditem;
        document.getElementById('update_code').innerHTML = item.dataset.code;
        document.getElementById('update_name').value = item.dataset.name;
        document.getElementById('update_value').value = item.dataset.value;
        document.getElementById('update_stock').value = item.dataset.stock;

        $('#updateItemModal').removeClass('hidden').addClass('flex');
        let img = $('#' + iditem + '-item').data('image');
        $('#preview').attr('src', img);
        $('body').addClass('overflow-y-hidden');
    }

    function closeUpdateModal() {
        $('#updateItemModal').addClass('hidden').removeClass('flex');
        $('#updateItemForm')[0].reset();
        $('body').removeClass('overflow-y-hidden');
    }

    function hideSuccessMessage() {
        $('#success').addClass('hidden').removeClass('flex');
    }

    function hideErrorMessage() {
        $('#error').addClass('hidden').removeClass('flex');
    }

    $(document).ready(function () {
        $('#header-title').text('Items');

        if (localStorage.getItem('addSuccess') === 'true') {
            message = localStorage.getItem('message');
            $('#success').addClass('flex').removeClass('hidden');
            $('#success-message').text(message);
            localStorage.removeItem('addSuccess');
            localStorage.removeItem('message');
        }

        if (localStorage.getItem('addError') === 'true') {
            message = localStorage.getItem('message');
            $('#error').addClass('flex').removeClass('hidden');
            $('#error-message').text(message);
            localStorage.removeItem('addError');
            localStorage.removeItem('message');
        }

        if (localStorage.getItem('updateSuccess') === 'true') {
            message = localStorage.getItem('message');
            $('#success').addClass('flex').removeClass('hidden');
            $('#success-message').text(message);
            localStorage.removeItem('updateSuccess');
            localStorage.removeItem('message');
        }

        if (localStorage.getItem('updateError') === 'true') {
            message = localStorage.getItem('message');
            $('#error').addClass('flex').removeClass('hidden');
            $('#error-message').text(message);
            localStorage.removeItem('updateError');
            localStorage.removeItem('message');
        }

        if (localStorage.getItem('deleteSuccess') === 'true') {
            message = localStorage.getItem('message');
            $('#success').addClass('flex').removeClass('hidden');
            $('#success-message').text(message);
            localStorage.removeItem('deleteSuccess');
            localStorage.removeItem('message');
        }

        if (localStorage.getItem('deleteError') === 'true') {
            message = localStorage.getItem('message');
            $('#error').addClass('flex').removeClass('hidden');
            $('#error-message').text(message);
            localStorage.removeItem('deleteError');
            localStorage.removeItem('message');
        }

        $('#update_image').on('change', function () {
            let file = this.files[0];

            if (file) {
                $('#preview').attr('src', URL.createObjectURL(file))
            }
        })

        $('#addItemForm').on('submit', function (event) {
            event.preventDefault();
            let data = new FormData($('#addItemForm')[0]);

            $('#loader').removeClass('hidden').addClass('flex');

            $.ajax({
                url: "logic/item_create.php",
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == 'success') {
                        localStorage.setItem("addSuccess", "true");
                        localStorage.setItem('message', response['message']);
                        location.reload();
                    } else {
                        localStorage.setItem("addError", "true");
                        localStorage.setItem('message', response['message']);
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    $('#error').addClass('flex').removeClass('hidden');
                    $('#error-message').text(response.message);
                    location.reload();
                    localStorage.setItem("addError", "true");
                }
            })
        })

        $('#updateItemForm').on('submit', function (event) {
            event.preventDefault();
            let data = new FormData($('#updateItemForm')[0]);

            $('#loader').removeClass('hidden').addClass('flex');

            $.ajax({
                url: "logic/item_update.php",
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == 'success') {
                        localStorage.setItem("updateSuccess", "true");
                        localStorage.setItem('message', response['message']);
                        location.reload();
                    } else {
                        localStorage.setItem("updateError", "true");
                        localStorage.setItem('message', response['message']);
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    $('#error').addClass('flex').removeClass('hidden');
                    $('#error-message').text(response.message);
                    location.reload();
                    localStorage.setItem("updateError", "true");
                }
            })
        })

        $(document).click(function () {
            if ($(event.target).closest('#addItemModal').length && !$(event.target).closest('#add-main').length) {
                closeAddModal();
            }
            if ($(event.target).closest('#updateItemModal').length && !$(event.target).closest('#update-main').length) {
                closeUpdateModal();
            }
            if ($(event.target).closest('#deleteItemModal').length && !$(event.target).closest('#delete-main').length) {
                closeDeleteModal();
            }
        })



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
    })
</script>

</html>