<?php
session_start();
include_once '..\api\includes\connect-db.php';

$usersPerPage = 3; 
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $usersPerPage;

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM user");
    $totalUsers = $stmt->fetch()['total'];
    $totalPages = ceil($totalUsers / $usersPerPage);

    $stmt = $pdo->prepare("SELECT * FROM user LIMIT :offset, :limit");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $usersPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();
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
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");

            sidebar.classList.toggle("-translate-x-full");
            menuButton.classList.toggle("hidden");
        }
    </script>
</head>
<body class="bg-gray-100 flex">
    <header class="bg-black text-white fixed top-0 left-0 w-full h-14 flex items-center px-4 shadow-md z-50">
        <button id="menuButton" onclick="toggleSidebar()" class="text-white text-2xl mr-4">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-lg font-semibold">Users Management</h1>
    </header>

    <aside id="sidebar"
        class="bg-black text-white w-64 h-screen p-6 absolute left-0 top-14 transform -translate-x-full transition-transform duration-300 z-40">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <img src="assets/logo.png" alt="MoneyMo Logo" class="h-8 w-8 mr-2">
                <h2 class="text-xl font-bold">MoneyMo</h2>
            </div>
            <button onclick="toggleSidebar()" class="text-white text-2xl">&times;</button>
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

    <button id="menuButton" onclick="toggleSidebar()" class="fixed left-2 top-4 bg-black text-white px-3 py-2 rounded">
        <i class="fas fa-bars"></i>
    </button>

    <main id="mainContent" class="mt-16 p-6 w-full transition-all duration-300">
        <div class="flex items-center justify-between bg-white p-4 shadow rounded-lg">
        <h1 class="text-2xl font-bold text-gray-800">Users</h1>
            <div class="flex items-center space-x-4">
                <div class="relative w-64">
                    <input type="text" id="adminSearch" onkeyup="searchTable('adminSearch', '.admin-table')" 
                        placeholder="Search Users"
                        class="border border-gray-300 p-2 pl-10 pr-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-black">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button onclick="document.getElementById('addUserModal').style.display='flex'"
                    class="bg-black text-white px-5 py-2 rounded-lg flex items-center hover:bg-gray-800 transition">
                    <i class="fas fa-user-plus text-lg mr-2"></i> Add User
                </button>
            </div>
        </div>

        <div class="mt-6 bg-white shadow rounded-lg p-4">
            <table class="min-w-full bg-white user-table">
            <thead>
            <tr class="bg-gray-200">
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">User Role</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($users)) {
            foreach ($users as $userData) {
                $fullName = $userData['f_name'] . ' ' . $userData['l_name'];
                $userRole = $userData['usertype'] == 1 ? "Admin" : "User";

                echo "<tr class='border-t'>";
                echo "<td class='px-6 py-3'>{$fullName}</td>";
                echo "<td class='px-6 py-3'>{$userRole}</td>";
                echo "<td class='px-6 py-3'>{$userData['email']}</td>";
                echo "<td class='px-6 py-3'>";

                echo "<a href='#' class='edit-btn text-black-500'
                    data-id='{$userData['iduser']}'
                    data-fullname='{$fullName}'
                    data-email='{$userData['email']}'
                    data-usertype='{$userData['usertype']}'>
                    <i class='fas fa-edit'></i>
                </a>";
                echo " | ";

                echo "<a href='views/logic/user_delete.php?id={$userData['iduser']}' class='text-black-500' 
                        onclick='return confirm(\"Are you sure you want to delete this user?\");'>
                        <i class='fas fa-trash-alt'></i>
                    </a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='px-6 py-3 text-center text-gray-500'>No users found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    
    <div class="flex justify-center mt-6">
        <nav class="flex space-x-2">
            <a href="?page=<?= max(1, $currentPage - 1) ?>" 
               class="px-3 py-2 border rounded <?= ($currentPage == 1) ? 'bg-gray-300 cursor-not-allowed' : 'hover:bg-gray-200' ?>">
                <i class="fas fa-chevron-left"></i>
            </a>

        <?php if ($totalPages <= 5): ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                    class="px-3 py-2 border rounded <?= ($i == $currentPage) ? 'bg-black text-white' : 'hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            <?php else: ?>
                <a href="?page=1" class="px-3 py-2 border rounded <?= ($currentPage == 1) ? 'bg-black text-white' : 'hover:bg-gray-200' ?>">1</a>
                <a href="?page=2" class="px-3 py-2 border rounded <?= ($currentPage == 2) ? 'bg-black text-white' : 'hover:bg-gray-200' ?>">2</a>
                <span class="px-3 py-2 border rounded bg-gray-100">...</span>
                <a href="?page=<?= $totalPages - 1 ?>" class="px-3 py-2 border rounded hover:bg-gray-200"><?= $totalPages - 1 ?></a>
                <a href="?page=<?= $totalPages ?>" class="px-3 py-2 border rounded hover:bg-gray-200"><?= $totalPages ?></a>
            <?php endif; ?>

            <a href="?page=<?= min($totalPages, $currentPage + 1) ?>" 
            class="px-3 py-2 border rounded <?= ($currentPage == $totalPages) ? 'bg-gray-300 cursor-not-allowed' : 'hover:bg-gray-200' ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        </nav>
    </div>
   

    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");
            let mainContent = document.getElementById("mainContent");

            sidebar.classList.toggle("-translate-x-full");
            menuButton.classList.toggle("hidden");

            if (!sidebar.classList.contains("-translate-x-full")) {
                mainContent.classList.add("ml-64");
            } else {
                mainContent.classList.remove("ml-64");
            }
        }
    </script>
</body>
</html>
