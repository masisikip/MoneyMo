<?php
include_once '../../includes/connect-db.php';

$usersPerPage = 8;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="">
    <?php include_once '../../includes/partial.php' ?>

    <main id="mainContent" class="mt-5 p-6 w-full max-w-full transition-all duration-300">
        <div class="flex items-center justify-between bg-white p-4 shadow rounded-lg gap-2">
            <div class="flex items-center gap-3 flex-grow">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Users</h1>
                <div class="relative w-full max-w-[180px] md:max-w-xs">
                    <input type="text" id="userSearch" onkeyup="searchTable('userSearch', '.user-table')"
                        placeholder="Search"
                        class="border border-gray-300 p-2 pl-8 pr-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-black text-base md:text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <button onclick="document.getElementById('addUserModal').style.display='flex'"
                class="bg-black text-white p-3 rounded-lg flex items-center hover:bg-gray-800 transition md:px-4 md:py-2 md:gap-2 justify-center">
                <i class="fas fa-user-plus text-xl"></i>
                <span class="hidden md:block text-sm font-medium">Add User</span>
            </button>
        </div>


        <div class="mt-6 bg-white shadow rounded-lg p-4 hidden md:block">
            <div class="overflow-x-auto">
                <?php if (!empty($users)): ?>
                    <!-- Desktop View (Table) -->
                    <table class="hidden md:table w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Name</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Student ID</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Email</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Usertype</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $userData): ?>
                                <tr class="border-t">
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left">
                                        <?= $userData['student_id'] ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['email']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['usertype'] == 1 ? 'Officer' : 'Student') ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left whitespace-nowrap">
                                        <a href="#"
                                            class="edit-btn text-black-500 text-xs md:text-base mx-1 inline-flex items-center"
                                            data-id="<?= $userData['iduser'] ?>"
                                            data-lname="<?= htmlspecialchars($userData['l_name']) ?>"
                                            data-fname="<?= htmlspecialchars($userData['f_name']) ?>"
                                            data-email="<?= htmlspecialchars($userData['email']) ?>"
                                            data-usertype="<?= htmlspecialchars($userData['usertype']) ?>"
                                            data-student_id="<?= htmlspecialchars($userData['student_id']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <span class="text-gray-400 mx-1">|</span>
                                        <a href="./logic/user_delete.php?id=<?= $userData['iduser'] ?>"
                                            class="text-black-500 text-xs md:text-base mx-1 inline-flex items-center"
                                            onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-gray-500 text-xs md:text-base">No users found.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($users)): ?>
            <div class="md:hidden space-y-4 mt-4">
                <?php foreach ($users as $userData): ?>
                    <div class="bg-white shadow-md border border-gray-200 rounded-lg p-5 flex justify-between items-center">
                        <div>
                            <!-- User Name -->
                            <p class="text-black-700 font-medium">
                                <?= htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']) ?>
                            </p>
                            <!-- User Student_id -->
                            <p class="font-bold text-sm <?= $userData['usertype'] == 1 ? 'text-black' : 'text-gray-500' ?>">
                                <?= $userData['student_id'] ?>
                            </p>
                            <!-- User Email -->
                            <p class="text-gray-500 text-sm"><?= htmlspecialchars($userData['email']) ?></p>
                        </div>
                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            <a href="#" class="edit-btn text-black text-xs md:text-base" data-id="<?= $userData['iduser'] ?>"
                                data-lname="<?= htmlspecialchars($userData['l_name']) ?>"
                                data-fname="<?= htmlspecialchars($userData['f_name']) ?>"
                                data-email="<?= htmlspecialchars($userData['email']) ?>"
                                data-usertype="<?= htmlspecialchars($userData['usertype']) ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="logic/user_delete.php?id=<?= $userData['iduser'] ?>"
                                class="text-black text-xs md:text-base"
                                onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 text-xs md:text-base">No users found.</p>
        <?php endif; ?>

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
                    <a href="?page=<?= $totalPages ?>"
                        class="px-3 py-2 border rounded hover:bg-gray-200"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-2 border rounded hover:bg-gray-200">Next »</a>
                <?php endif; ?>
            </nav>
        </div>

    </main>

    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center bg-[#00000078] hidden">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Add User</h2>
            <form id="addUserForm" action="" method="POST">
                <input type="text" name="f_name" placeholder="First Name" required
                    class="w-full p-2 border rounded mb-2">
                <input type="text" name="l_name" placeholder="Last Name" required
                    class="w-full p-2 border rounded mb-2">
                <input type="email" name="email" placeholder="Email" pattern=".*@psu.palawan.edu.ph"
                    title="Email should be a PSU corporate one." required class="w-full p-2 border rounded mb-2">
                <input type="text" name="student_id" placeholder="Student ID"
                    pattern="20[0-9]{2}-[0-9]+-[0-9]{4}([A-Z]{2})?" title="Format: 20##-#-#### or 20##-#-####XX"
                    required class="w-full p-2 border rounded mb-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_admin" value="1">
                    <span>Officer</span>
                </label>
                <button type="submit" class="bg-black text-white w-full py-2 rounded mt-2">Add User</button>
            </form>
            <button id="cancelButton" class="bg-gray-400 text-white w-full py-2 rounded mt-2">Cancel</button>
        </div>
    </div>


    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-[#00000078] hidden">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Edit User/Admin</h2>
            <form id="editForm" method="POST" action="./logic/user_admin_edit.php">
                <input type="hidden" name="user_id" id="edit_user_id">

                <label class="block text-gray-700">Last Name:</label>
                <input type="text" name="l_name" id="edit_lname" class="border p-2 w-full rounded mb-2" required>

                <label class="block text-gray-700">First Name:</label>
                <input type="text" name="f_name" id="edit_fname" class="border p-2 w-full rounded mb-2" required>

                <label class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="edit_email" class="border p-2 w-full rounded mb-2"
                    pattern=".*@psu.palawan.edu.ph" title="Email should be a PSU corporate one." required>

                <label class="block text-gray-700">Student ID:</label>
                <input id="edit_student_id" name="student_id" class="w-full p-2 border rounded mb-2"
                    pattern="20[0-9]{2}-[0-9]+-[0-9]{4}([A-Z]{2})?" title="Format: 20##-#-#### or 20##-#-####XX"
                    required>

                <label class="block text-gray-700">New Password (leave blank to keep current):</label>
                <input type="password" name="password" id="edit_password" class="border p-2 w-full rounded mb-2">

                <div class="flex items-center space-x-2 mb-4">
                    <input type="checkbox" name="usertype" id="edit_usertype_checkbox" class="h-4 w-4">
                    <label for="edit_usertype_checkbox" class="text-gray-700">Officer</label>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="submit" name="update_user"
                        class="bg-black text-white px-4 py-2 rounded">Update</button>
                    <button type="button" id="closeModal"
                        class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
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
                <button onclick="hideErrorMessage()" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 cursor-pointer">Okay</button>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader"
        class="fixed top-0 w-full h-full bg-gray-500/50 backdrop-blur-xs justify-center items-center hidden">
        <div class="w-16 h-16 border-6 border-t-gray-800 border-gray-300 rounded-full animate-spin"></div>
    </div>


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

            document.getElementById("noResultsMessage").style.display = found ? "none" : "block";
        }

        function addUser() {
            let data = new FormData($('#addUserForm')[0]);

            // Show loader
            $('#loader').addClass('flex').removeClass('hidden');

            $.ajax({
                url: 'logic/user_add.php',
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {
                    response = JSON.parse(response);

                    $('#loader').removeClass('flex').addClass('hidden');

                    if (response.status === 'success') {
                        location.reload();
                        localStorage.setItem("addSuccess", "true");
                    } else {
                        $('#error').addClass('flex').removeClass('hidden');
                        $('#error-message').text(response.message);
                        location.reload();
                        localStorage.setItem("addError", "true");
                    }
                },
                error: function (xhr, status, error) {
                    $('#error').addClass('flex').removeClass('hidden');
                    $('#error-message').text('Failed to connect to server.');
                    location.reload();
                    localStorage.setItem("addError", "true");
                }
            });
        }


        function hideSuccessMessage() {
            $('#success').addClass('hidden').removeClass('flex');
        }

        function hideErrorMessage() {
            $('#error').addClass('hidden').removeClass('flex');
        }


        document.addEventListener("DOMContentLoaded", function () {
            console.log("DOM fully loaded and parsed.");

            const addUserModal = document.getElementById("addUserModal");
            const editModal = document.getElementById("editModal");
            const addUserForm = document.getElementById("addUserForm");
            const cancelButton = document.getElementById("cancelButton");
            const closeModal = document.getElementById("closeModal");

            if (!addUserModal || !editModal) {
                console.error("Modal elements not found in the DOM.");
                return;
            }

            document.getElementById("openAddUserModal")?.addEventListener("click", function () {
                console.log("Open Add User Modal clicked.");
                addUserModal.style.display = "none";
            });

            cancelButton?.addEventListener("click", function () {
                console.log("Cancel button clicked. Hiding modal...");
                addUserForm?.reset();
                addUserModal.style.display = "none";
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    console.log("Edit button clicked for ID:", this.dataset.id);

                    document.getElementById('edit_user_id').value = this.dataset.id;
                    document.getElementById('edit_lname').value = this.dataset.lname;
                    document.getElementById('edit_fname').value = this.dataset.fname;
                    document.getElementById('edit_student_id').value = this.dataset.student_id;
                    document.getElementById('edit_email').value = this.dataset.email;

                    // Check if user is admin
                    document.getElementById('edit_usertype_checkbox').checked = (this.dataset.usertype === "1");

                    editModal.classList.remove('hidden');
                });
            });


            closeModal?.addEventListener("click", function () {
                console.log("Close Edit Modal clicked.");
                editModal.classList.add("hidden");
            });

            window.addEventListener("click", (event) => {
                if (event.target === addUserModal) {
                    console.log("Clicked outside Add User Modal. Closing...");
                    addUserForm?.reset();
                    addUserModal.style.display = "none";
                }
                if (event.target === editModal) {
                    console.log("Clicked outside Edit Modal. Closing...");
                    editModal.classList.add("hidden");
                }
            });
        });

        $(document).ready(function () {
            if (localStorage.getItem('addSuccess') === 'true') {
                $('#success').addClass('flex').removeClass('hidden');
                $('#success-message').text('Successfully added user');
                localStorage.removeItem('addSuccess');
            }

            if (localStorage.getItem('addError') === 'true') {
                $('#error').addClass('flex').removeClass('hidden');
                $('#error-message').text('Failed to add user');
                localStorage.removeItem('addError');
            }

            $('#addUserForm').on('submit', function (e) {
                e.preventDefault();
                addUser();
            })
        })
    </script>


</body>

</html>