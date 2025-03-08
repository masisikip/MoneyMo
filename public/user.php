<?php
session_start();
include_once '../api/includes/connect-db.php';

$usersPerPage = 3;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $usersPerPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE name LIKE :search OR email LIKE :search");
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->execute();
        $users = $stmt->fetchAll();

        $stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM user WHERE name LIKE :search OR email LIKE :search");
        $stmtCount->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmtCount->execute();
        $totalSearchResults = $stmtCount->fetch()['total'];

        $totalPages = 1;
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM user");
        $totalUsers = $stmt->fetch()['total'];

        $stmt = $pdo->prepare("SELECT * FROM user LIMIT :offset, :usersPerPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':usersPerPage', $usersPerPage, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        $totalPages = ceil($totalUsers / $usersPerPage);
    }
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
            let mainContent = document.getElementById("mainContent");

            sidebar.classList.toggle("-translate-x-full");
            mainContent.classList.toggle("ml-64");
        }
    </script>
    <script>
        function searchTable(inputId, tableClass) {
            let input = document.getElementById(inputId);
            let filter = input.value.toLowerCase();
            let table = document.querySelector(tableClass);
            let rows = table.getElementsByTagName("tr");

            let found = false;
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
                if (match) found = true;
            }

            //no results message
            if (!found) {
                document.getElementById("noResultsMessage").style.display = "block";
            } else {
                document.getElementById("noResultsMessage").style.display = "none";
            }
        }


        // cancel button
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

        //edit
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

        function cancelForm() {
            document.getElementById("addUserForm").reset(); 
            document.getElementById("addUserModal").style.display = 'none'; 
        }

    </script>
</head>
<body class="bg-gray-100 flex overflow-x-hidden">
    <header class="bg-black text-white fixed top-0 left-0 w-full h-14 flex items-center px-4 shadow-md z-50 md:px-6">
        <button id="menuButton" onclick="toggleSidebar()" class="text-white text-2xl mr-4">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-lg font-semibold">Users Management</h1>
    </header>

    <aside id="sidebar"
        class="bg-black text-white w-64 h-screen fixed left-0 top-0 transform -translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center justify-between mb-6 p-4">
            <img src="assets/logo.png" alt="Logo" class="h-8 w-8">
            <h2 class="text-xl font-bold">MoneyMo</h2>
            <button onclick="toggleSidebar()" class="text-white text-2xl">&times;</button>
        </div>
        <nav>
            <ul class="space-y-3">
                <li><a href="dashboard_admin.php" class="flex items-center p-3 hover:bg-gray-600 rounded"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a></li>
                <li><a href="inventory.php" class="flex items-center p-3 hover:bg-gray-600 rounded"><i class="fas fa-boxes mr-2"></i> Inventory</a></li>
                <li><a href="item.php" class="flex items-center p-3 hover:bg-gray-600 rounded"><i class="fas fa-cubes mr-2"></i> Item Management</a></li>
                <li><a href="user.php" class="flex items-center p-3 hover:bg-gray-600 rounded"><i class="fas fa-users-cog mr-2"></i> User Management</a></li>
                <li><a href="logout.php" class="flex items-center p-3 hover:bg-gray-600 rounded"><i class="fas fa-sign-out-alt mr-2"></i> Log Out</a></li>
            </ul>
        </nav>
    </aside>

    <main id="mainContent" class="mt-16 p-6 w-full max-w-full transition-all duration-300">
        <div class="flex items-center justify-between bg-white p-4 shadow rounded-lg gap-2">
            <div class="flex items-center gap-3 w-full">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Users</h1>
                <div class="relative w-full max-w-[180px] md:max-w-xs">
                    <input type="text" id="userSearch" onkeyup="searchTable('userSearch', '.user-table')" 
                        placeholder="Search"
                        class="border border-gray-300 p-2 pl-8 pr-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-black text-base md:text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <button onclick="document.getElementById('addUserModal').style.display='flex'"
                class="bg-black text-white p-3 rounded-lg flex items-center hover:bg-gray-800 transition w-12 h-12 md:w-auto md:px-4 md:py-2 md:gap-2 justify-center">
                <i class="fas fa-user-plus text-xl"></i>
                <span class="hidden md:block text-sm font-medium">Add User</span>
            </button>
        </div>


        <div class="mt-6 bg-white shadow rounded-lg p-4">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="w-1/4 px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">Name</th>
                            <th class="w-1/4 px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">User Role</th>
                            <th class="w-1/4 px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">Email</th>
                            <th class="w-1/4 px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $userData): ?>
                                <tr class="border-t">
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left">
                                        <span class="<?= $userData['usertype'] == 1 ? 'bg-black' : 'bg-gray-400' ?> 
                                                    text-white px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm inline-block w-20 md:w-24 text-center">
                                            <?= $userData['usertype'] == 1 ? "Admin" : "User" ?>
                                        </span>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['email']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left whitespace-nowrap">
                                        <a href="#" class="edit-btn text-black-500 text-xs md:text-base mx-1 inline-flex items-center"
                                            data-id="<?= $userData['iduser'] ?>"
                                            data-lname="<?= htmlspecialchars($userData['l_name']) ?>"
                                            data-fname="<?= htmlspecialchars($userData['f_name']) ?>"
                                            data-email="<?= htmlspecialchars($userData['email']) ?>"
                                            data-usertype="<?= htmlspecialchars($userData['usertype']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <span class="text-gray-400 mx-1">|</span>
                                        <a href="views/logic/user_delete.php?id=<?= $userData['iduser'] ?>" 
                                            class="text-black-500 text-xs md:text-base mx-1 inline-flex items-center" 
                                            onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-2 md:px-6 py-3 text-center text-gray-500 text-xs md:text-base">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        $range = 2;
        ?>
        <div class="flex justify-center mt-6">
            <nav class="flex space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-2 border rounded hover:bg-gray-200">« Prev</a>
                <?php endif; ?>

                <?php if ($currentPage > ($range + 1)): ?>
                    <a href="?page=1" class="px-3 py-2 border rounded hover:bg-gray-200">1</a>
                    <span class="px-3 py-2">...</span>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++): ?>
                    <a href="?page=<?= $i ?>" 
                    class="px-3 py-2 border rounded <?= ($i == $currentPage) ? 'bg-black text-white' : 'hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages - $range): ?>
                    <span class="px-3 py-2">...</span>
                    <a href="?page=<?= $totalPages ?>" class="px-3 py-2 border rounded hover:bg-gray-200"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-2 border rounded hover:bg-gray-200">Next »</a>
                <?php endif; ?>
            </nav>
        </div>

    </main>

    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Add User</h2>
            <form id="addUserForm" action="views/logic/user_add.php" method="POST">
                <input type="text" name="f_name" placeholder="First Name" required class="w-full p-2 border rounded mb-2">
                <input type="text" name="l_name" placeholder="Last Name" required class="w-full p-2 border rounded mb-2">
                <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
                <button type="submit" class="bg-black text-white w-full py-2 rounded">Add User</button>
            </form>
            <button onclick="cancelForm()" class="bg-gray-400 text-white w-full py-2 rounded mt-2">Cancel</button>
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
