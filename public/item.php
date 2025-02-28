<?php
include_once __DIR__ . '\..\api\includes\connect-db.php';

try {
    $stmt = $pdo->query("SELECT * FROM item");
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal {
            z-index: 50;
        }
        .item-image {
            width: 100%;
            height: 150px; 
            object-fit: cover;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown-menu {
            display: none;
        }
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.show {
            transform: translateX(0);
        }
        .content {
            transition: margin-left 0.3s ease-in-out;
        }
        .content.shifted {
            margin-left: 16rem; /* Width of the sidebar */
        }
        .icon-button {
            transition: background-color 0.3s, color 0.3s;
        }
        .icon-button:hover {
            background-color: #000000;
            color: #ffffff;
        }
        @media (max-width: 640px) {
            .add-item-button {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 2rem; 
                height: 2rem;
                padding: 0;
            }
            .add-item-button i {
                margin-right: 0;
            }
            .add-item-button span {
                display: none;
            }
            .modal-content {
                width: 66.67%; 
            }
        }
        @media (min-width: 641px) {
            .modal-content {
                width: 25%; 
            }
        }
    </style>
</head>
<body class="flex flex-col h-screen bg-gray-100">
    <header class="bg-black text-white p-6 flex justify-between items-center">
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="text-white focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <span class="ml-4 text-3xl font-bold">Item Manager</span> <!-- Increased font size -->
        </div>
        <button class="text-black bg-white px-4 py-2 rounded flex items-center add-item-button" onclick="toggleModal('addItemModal')">
            <i class="fas fa-plus mr-2"></i> <span>Add Item</span>
        </button>
    </header>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-black text-white p-6 sidebar">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Menu</h2>
            <button onclick="toggleSidebar()" class="text-white focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <nav>
            <ul class="space-y-3">
                <li>
                    <a href="dashboard_admin.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="inventory.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-boxes mr-2"></i> Inventory
                    </a>
                </li>
                <li>
                    <a href="item.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-cubes mr-2"></i> Item Management
                    </a>
                </li>
                <li>
                    <a href="user.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-users-cog mr-2"></i> User Management
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div id="content" class="flex-1 p-8 content">
        <!-- Item Grid -->
        <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
            <?php foreach ($items as $item): ?>
                <div id="<?= $item['iditem'] ?>-item" data-iditem="<?= $item['iditem'] ?>"
                    data-code="<?= $item['code'] ?>" data-name="<?= $item['name'] ?>"
                    data-value="<?= $item['value'] ?>"
                    data-image="data:image/jpeg;base64,<?= base64_encode($item['image']); ?>"
                    class="cursor-pointer flex flex-col justify-center items-center relative bg-white border border-gray-300 rounded-lg hover:scale-105 transition-transform duration-300 ease-in-out lg:min-h-[15rem] lg:min-w-[12rem] lg:max-w-[20rem] h-auto md:min-h-[10rem] md:min-w-[10rem] md:max-w-[15rem]">

                    <!-- Item Image -->
                    <?php if (!empty($item['image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" alt="Item Image"
                            class="object-cover w-full h-full rounded-lg item-image">
                    <?php endif; ?>

                    <!-- Item Title Overlay -->
                    <div class="absolute top-0 left-0 right-0 flex items-center justify-between h-16 px-3 text-black">
                        <div class="flex rounded-full bg-[#ffffffa8] border border-zinc-700/60">
                            <div onclick="openUpdateModal(<?= $item['iditem'] ?>)"
                                class="flex justify-center w-1/2 h-full px-2 py-1 text-gray-600 border-r rounded-l-full cursor-pointer border-r-gray-600 hover:bg-black">
                                <i class="fa-solid fa-pen-to-square hover:text-gray-800"></i>
                            </div>
                            <div onclick="confirmDelete(<?= $item['iditem'] ?>)"
                                class="flex justify-center w-1/2 h-full px-2 py-1 text-gray-600 rounded-r-full cursor-pointer hover:bg-black">
                                <i class="hover:text-gray-800 fa-solid fa-trash"></i>
                            </div>
                        </div>
                        <h3 class="text-xl" style="font-family: Arial, sans-serif;">
                            <?php echo htmlspecialchars($item['value']); ?>
                        </h3>
                    </div>

                    <!-- Item Name -->
                    <div class="absolute bottom-0 left-0 right-0 flex items-center justify-center h-16 px-3 text-white rounded-b-lg bg-gradient-to-t from-black to-transparent">
                        <h3 class="text-xl" style="font-family: Arial, sans-serif;">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg modal-content">
            <h2 class="text-xl font-bold mb-4">Add New Item</h2>
            <form id="addItemForm" action="views/logic/item_create.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="code" class="block text-gray-700">Code <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="value" class="block text-gray-700">Value <span class="text-red-500">*</span></label>
                    <input type="number" id="value" name="value" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Image <span class="text-red-500">*</span></label>
                    <input type="file" id="image" name="image" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded mr-2" onclick="toggleModal('addItemModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Item Modal -->
    <div id="updateItemModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg modal-content">
            <h2 class="text-xl font-bold mb-4">Update Item</h2>
            <form id="updateItemForm" action="views/logic/item_update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="update_iditem" name="iditem">
                <div class="mb-4">
                    <label for="update_code" class="block text-gray-700">Code</label>
                    <input type="text" id="update_code" name="code" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="update_name" class="block text-gray-700">Name</label>
                    <input type="text" id="update_name" name="name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="update_value" class="block text-gray-700">Value</label>
                    <input type="number" id="update_value" name="value" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="update_image" class="block text-gray-700">Image</label>
                    <input type="file" id="update_image" name="image" class="w-full p-2 border border-gray-300 rounded mt-1">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded mr-2" onclick="toggleModal('updateItemModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

</body>
<script>
function toggleModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle("hidden");
        if (modal.classList.contains("hidden")) {
            clearModalInputs(modalId);
        }
    }
}

document.querySelectorAll('.fixed.inset-0').forEach(modal => {
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
            clearModalInputs(modal.id);
        }
    });
});

function clearModalInputs(modalId) {
    const modal = document.getElementById(modalId);
    const inputs = modal.querySelectorAll('input');
    inputs.forEach(input => {
        if (input.type !== 'hidden') {
            input.value = '';
        }
    });
    const fileInputs = modal.querySelectorAll('input[type="file"]');
    fileInputs.forEach(fileInput => {
        fileInput.value = '';
    });
}

function confirmDelete(iditem) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch('views/logic/item_delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `iditem=${iditem}`
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
}

document.getElementById('addItemForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('views/logic/item_create.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        toggleModal('addItemModal');
        location.reload();
    })
    .catch(error => console.error('Error:', error));
});

function openUpdateModal(iditem) {
    const item = document.getElementById(`${iditem}-item`);
    document.getElementById('update_iditem').value = item.dataset.iditem;
    document.getElementById('update_code').value = item.dataset.code;
    document.getElementById('update_name').value = item.dataset.name;
    document.getElementById('update_value').value = item.dataset.value;
    toggleModal('updateItemModal');
}

document.getElementById('updateItemForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('views/logic/item_update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        toggleModal('updateItemModal');
        location.reload();
    })
    .catch(error => console.error('Error:', error));
});

function toggleSidebar() {
    let sidebar = document.getElementById('sidebar');
    let content = document.getElementById('content');
    if (sidebar && content) {
        sidebar.classList.toggle("show");
        content.classList.toggle("shifted");
    }
}
</script>
</html>