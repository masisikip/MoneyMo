<?php
session_start();
include_once '.\includes\connect-db.php';

try {
    $stmt = $pdo->query("SELECT * FROM user");
    $user = $stmt->fetchAll();
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
    <script>
        function searchTable(inputId, tableClass) {
            let input = document.getElementById(inputId);
            let filter = input.value.toLowerCase();
            let table = document.querySelector(tableClass);
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName("td");
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? "" : "none";
            }
        }
        //for add modal cancel button
        document.addEventListener("DOMContentLoaded", function () {
            const cancelButton = document.querySelector("#addUserModal #cancelButton");
            const modal = document.getElementById("addUserModal");

            if (cancelButton && modal) {
                cancelButton.addEventListener("click", function () {
                    console.log("Cancel button clicked! Hiding modal...");
                    modal.style.display = "none";
                });
            } else {
                console.error("Cancel button or modal not found!");
            }
        });
        // notice message
        setTimeout(function() {
            var alertBox = document.getElementById("alertMessage");
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 5000);
        //for edit modal
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('edit_user_id').value = this.dataset.id;
                    document.getElementById('edit_lname').value = this.dataset.lname;
                    document.getElementById('edit_fname').value = this.dataset.fname;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_usertype').value = this.dataset.usertype === "1" ? "Admin" : "User";
                    document.getElementById('editModal').classList.remove('hidden');
                });
            });
            document.getElementById('closeModal').addEventListener('click', function () {
                document.getElementById('editModal').classList.add('hidden');
            });
            document.getElementById('editModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

    </script>
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex h-screen bg-gray-100">
    <aside class="w-64 bg-black text-white p-6">
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

    <div class="flex-1 p-8">

        <?php if (isset($_SESSION['message'])): ?>
            <div id="alertMessage" class="p-3 mb-4 rounded <?php echo ($_SESSION['message_type'] == 'success') ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700'; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); 
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>User Management</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
            <button onclick="document.getElementById('addUserModal').style.display='flex'" class="bg-black text-white px-4 py-2 rounded flex items-center">
                <span class="text-lg font-bold mr-2">+</span> Add User
            </button>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2">Admins</h2>
            <input type="text" id="adminSearch" onkeyup="searchTable('adminSearch', '.admin-table')" placeholder="Search Admins..." class="border p-2 w-full max-w-md mb-3 rounded">
            <table class="min-w-full bg-white shadow rounded-lg admin-table">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-3 text-left">Last Name</th>
                        <th class="px-6 py-3 text-left">First Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($user)) {
                        foreach ($user as $admin) {
                            if ($admin['usertype'] == 1) {
                                echo "<tr class='border-t'>";
                                echo "<td class='px-6 py-3'>{$admin['l_name']}</td>";
                                echo "<td class='px-6 py-3'>{$admin['f_name']}</td>";
                                echo "<td class='px-6 py-3'>{$admin['email']}</td>";
                                echo "<td class='px-6 py-3'>";
                                echo "<a href='#' class='edit-btn text-green-500'
                                        data-id='{$admin['iduser']}'
                                        data-lname='{$admin['l_name']}'
                                        data-fname='{$admin['f_name']}'
                                        data-email='{$admin['email']}'
                                        data-usertype='{$admin['usertype']}'>
                                        <i class='fas fa-edit'></i> 
                                    </a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='4' class='px-6 py-3 text-center text-gray-500'>No admins found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2">Users</h2>
            <input type="text" id="userSearch" onkeyup="searchTable('userSearch', '.user-table')" placeholder="Search Users..." class="border p-2 w-full max-w-md mb-3 rounded">
            <table class="min-w-full bg-white shadow rounded-lg user-table">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-6 py-3 text-left">Last Name</th>
                        <th class="px-6 py-3 text-left">First Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($user)) {
                        foreach ($user as $userData) {
                            if ($userData['usertype'] == 0) { 
                                echo "<tr class='border-t'>";
                                echo "<td class='px-6 py-3'>{$userData['l_name']}</td>";
                                echo "<td class='px-6 py-3'>{$userData['f_name']}</td>";
                                echo "<td class='px-6 py-3'>{$userData['email']}</td>";
                                echo "<td class='px-6 py-3'>";
                                echo "<a href='#' class='edit-btn text-green-500'
                                        data-id='{$userData['iduser']}'
                                        data-lname='{$userData['l_name']}'
                                        data-fname='{$userData['f_name']}'
                                        data-email='{$userData['email']}'
                                        data-usertype='{$userData['usertype']}'>
                                        <i class='fas fa-edit'></i>
                                    </a>";
                                echo "|";
                                echo "<a href='views/logic/user_delete.php?id={$userData['iduser']}' class='text-red-500' 
                                        onclick='return confirm(\"Are you sure you want to delete this user?\");'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='4' class='px-6 py-3 text-center text-gray-500'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- add User Modal -->
    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-[50rem] relative">
            <h2 class="text-xl font-bold mb-4">Add User</h2>
            <form action="views/logic/user_add.php" method="POST">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="l_name" placeholder="Last Name" class="border p-2 rounded" required>
                    <input type="text" name="f_name" placeholder="First Name" class="border p-2 rounded" required>
                    <input type="email" name="email" placeholder="Email" class="border p-2 rounded" required>
                </div>
                <div class="flex flex-col gap-2 mt-6">
                    <button type="submit" class="bg-black text-white px-4 py-2 rounded w-full">Add</button>
                    <button id="cancelButton" type="button" class="bg-red-500 text-white px-4 py-2 rounded w-full">Cancel</button>
                </div>
            </form>
        </div>  
    </div>
    <!-- Edit User Modal -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Edit User/Admin</h2>
            <form id="editForm" method="POST" action="views/logic/user_admin_edit.php">
                <input type="hidden" name="user_id" id="edit_user_id">
                <label class="block text-gray-700">Last Name:</label>
                <input type="text" name="l_name" id="edit_lname" class="border p-2 w-full rounded mb-2" required>
                <label class="block text-gray-700">First Name:</label>
                <input type="text" name="f_name" id="edit_fname" class="border p-2 w-full rounded mb-2" required>
                <label class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="edit_email" class="border p-2 w-full rounded mb-2" required>
                <label class="block text-gray-700">User Type:</label>
                <input type="text" id="edit_usertype" class="border p-2 w-full rounded mb-4 bg-gray-200" readonly>
                <div class="flex justify-end space-x-2">
                    <button type="submit" name="update_user" class="bg-black text-white px-4 py-2 rounded">Update</button>
                    <button type="button" id="closeModal" class="bg-black text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
