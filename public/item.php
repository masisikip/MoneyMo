<?php
include_once 'C:\xampp\htdocs\masisikip\MoneyMo\api\includes\connect-db.php';

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
</head>
<body class="flex h-screen bg-gray-100">
    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/4 h-auto">
            <h2 class="text-xl font-bold mb-4">Add New Item</h2>
            <form id="addItemForm">
                <div class="mb-4">
                    <label for="code" class="block text-gray-700">Code</label>
                    <input type="text" id="code" name="code" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="value" class="block text-gray-700">Value</label>
                    <input type="number" id="value" name="value" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-black text-white rounded mr-2" onclick="toggleModal('addItemModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Add Item</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Add Item Modal -->

    <!-- Update Item Modal -->
    <div id="updateItemModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/4 h-auto">
            <h2 class="text-xl font-bold mb-4">Update Item</h2>
            <form id="updateItemForm">
                <input type="hidden" id="updateId" name="id">
                <div class="mb-4">
                    <label for="updateCode" class="block text-gray-700">Code</label>
                    <input type="text" id="updateCode" name="code" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="updateName" class="block text-gray-700">Name</label>
                    <input type="text" id="updateName" name="name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label for="updateValue" class="block text-gray-700">Value</label>
                    <input type="number" id="updateValue" name="value" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="px-4 py-2 bg-black text-white rounded mr-2" onclick="toggleModal('updateItemModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Update Item</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Update Item Modal -->

    <aside class="w-64 bg-black text-white p-6 overflow-y-auto fixed h-full">
        <div class="flex items-center mb-10 mt-6">
            <img src="assets/logo.png" alt="MoneyMo Logo" class="h-8 w-8 mr-2">
            <h2 class="text-xl font-bold">MoneyMo</h2>
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

    <div class="flex-1 p-8 ml-64">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>Item Management</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, Admin!</span>
            </h1>
        </div>
        <div class="bg-white shadow p-4 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold flex items-center">
                    Items
                    <button class="ml-4 text-black flex items-center" onclick="toggleModal('addItemModal')">
                        <i class="fas fa-plus"></i>
                    </button>
                </h2>
            </div>
            <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
                <?php foreach ($items as $item): ?>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p><?php echo htmlspecialchars($item['name']); ?> <span class="text-gray-600 float-right"><?php echo htmlspecialchars($item['value']); ?> PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded" onclick="openUpdateModal(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded" onclick="confirmDelete(<?php echo $item['iditem']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    function openUpdateModal(item) {
        document.getElementById('updateId').value = item.iditem;
        document.getElementById('updateCode').value = item.code;
        document.getElementById('updateName').value = item.name;
        document.getElementById('updateValue').value = item.value;
        toggleModal('updateItemModal');
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            fetch('views/logic/item_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
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
</script>
</html>
