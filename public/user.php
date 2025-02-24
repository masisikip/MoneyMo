<?php
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
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>User Management</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
            <h1 class="text-2xl font-bold">User Management</h1>
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
                    <tr class="border-t">
                        <td class="px-6 py-3">Labotoy</td>
                        <td class="px-6 py-3">John Vincent</td>
                        <td class="px-6 py-3">cent@gmail.com</td>
                        <td class="px-6 py-3">
                            <a href="#" class="text-red-500">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
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
                    <tr class="border-t">
                        <td class="px-6 py-3">Realubit</td>
                        <td class="px-6 py-3">Karen Angela</td>
                        <td class="px-6 py-3">nene@gmail.com</td>
                        <td class="px-6 py-3">
                            <a href="#" class="text-red-500">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-[50rem]">
            <h2 class="text-xl font-bold mb-4">Profile</h2>
            <form>
                <div class="grid grid-cols-5 gap-4">
                    <input type="text" placeholder="Last Name" class="border p-2 col-span-2 rounded" required>
                    <input type="text" placeholder="First Name" class="border p-2 col-span-2 rounded" required>
                    <input type="text" placeholder="Middle Initial" class="border p-2 rounded" required>
                </div>
                <div class="grid grid-cols-5 gap-4 mt-4">
                    <input type="email" placeholder="Email" class="border p-2 col-span-3 rounded" required>
                    <input type="text" placeholder="Phone Number" class="border p-2 col-span-2 rounded" required>
                </div>
                <div class="grid grid-cols-5 gap-4 mt-4">
                    <input type="text" placeholder="Program" class="border p-2 col-span-3 rounded" required>
                    <select class="border p-2 col-span-1 rounded" required>
                        <option>First Year</option>
                        <option>Second Year</option>
                        <option>Third Year</option>
                        <option>Fourth Year</option>
                    </select>
                    <select class="border p-2 col-span-1 rounded" required>
                        <option>Block 1</option>
                        <option>Block 2</option>
                        <option>Block 3</option>
                    </select>
                </div>
                <button type="submit" class="bg-black text-white px-4 py-2 rounded w-full mt-6">Add</button>
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="mt-2 w-full text-gray-600">Cancel</button>
            </form>
        </div>
    </div>
</body>
</html>