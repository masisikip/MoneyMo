<?php
include_once '../../includes/connect-db.php';

$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$years = $_GET['year'] ?? [];
$usertypes = $_GET['usertype'] ?? [];

$whereClause = "WHERE 1 = 1";
$params = [];

// Search
if (!empty($search)) {
    $whereClause .= " AND (email LIKE ? OR f_name LIKE ? OR l_name LIKE ? OR student_id LIKE ?)";
    $searchTerm = "%$search%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

// Filter: Years
if (!empty($years)) {
    $in = implode(',', array_fill(0, count($years), '?'));
    $whereClause .= " AND year IN ($in)";
    $params = array_merge($params, $years);
}

// Filter: Usertypes
if (!empty($usertypes)) {
    $in = implode(',', array_fill(0, count($usertypes), '?'));
    $whereClause .= " AND usertype IN ($in)";
    $params = array_merge($params, $usertypes);
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM user $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $i => $val) {
    $countStmt->bindValue($i + 1, $val);
}
$countStmt->execute();
$total_records = $countStmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Get actual user data
$query = "
SELECT 
    iduser,
    f_name,
    l_name,
    student_id,
    email,
    year,
    CASE 
        WHEN year = 0 THEN 'unknown'
        WHEN year = 1 THEN '1st'
        WHEN year = 2 THEN '2nd'
        WHEN year = 3 THEN '3rd'
        WHEN year = 4 THEN '4th'
    END AS yeartext,
    usertype
FROM user
$whereClause
ORDER BY year, l_name
LIMIT ? OFFSET ?
";
$stmt = $pdo->prepare($query);

// Bind all dynamic filters
foreach ($params as $i => $val) {
    $stmt->bindValue($i + 1, $val);
}
// Add limit and offset
$stmt->bindValue(count($params) + 1, (int) $limit, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="">
    <?php include_once '../../includes/partial.php' ?>

    <main id="mainContent" class="mt-5 p-6 w-full max-w-full transition-all duration-300">
        <div class="flex items-center justify-between bg-white p-4 shadow rounded-lg relative">
            <!-- Left: Title, Search, Filter -->
            <div class="flex flex-col md:flex-row p-1 gap-4 flex-wrap flex-grow">
                <div class="flex flex-1 gap-2 max-w-[40rem]">
                    <!-- Search -->
                    <div class="relative w-full flex items-center">
                        <input type="text" id="userSearch" placeholder="Search" autocomplete="off" value="<?php if (isset($_GET['search'])) {
                            echo $_GET['search'];
                        } ?>"
                            class="border border-gray-300 py-2 pl-10 pr-4 rounded-lg w-full focus:outline-none focus:ring focus:ring-black text-base md:text-sm h-full">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 mr-2"></i>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative">
                        <button id="toggleFilterMenu"
                            class="bg-gray-100 text-black p-3 rounded-lg flex items-center hover:bg-gray-200 transition">
                            <i class="fa-solid fa-sliders text-xl"></i>
                        </button>

                        <div id="filterMenu"
                            class="hidden absolute top-full mt-2 right-0 bg-white shadow-lg border p-4 rounded-lg z-20 w-64">
                            <form id="filterForm">
                                <div class="mb-4">
                                    <label class="font-semibold mb-2 block">Year</label>
                                    <label><input type="checkbox" name="year[]" value="1"> 1st</label><br>
                                    <label><input type="checkbox" name="year[]" value="2"> 2nd</label><br>
                                    <label><input type="checkbox" name="year[]" value="3"> 3rd</label><br>
                                    <label><input type="checkbox" name="year[]" value="4"> 4th</label>
                                </div>
                                <div class="mb-4">
                                    <label class="font-semibold mb-2 block">User Type</label>
                                    <label><input type="checkbox" name="usertype[]" value="0"> Student</label><br>
                                    <label><input type="checkbox" name="usertype[]" value="1"> Officer</label>
                                </div>
                                <button type="submit"
                                    class="w-full mt-2 bg-black text-white py-2 rounded hover:bg-gray-800">Apply
                                    Filters</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="flex flex-grow justify-end">
                    <!-- Right: Add User Button -->
                    <button id="openAddUserModal"
                        class="w-full bg-black text-white rounded-lg flex items-center hover:bg-gray-800 transition md:w-fit md:px-4 py-2 gap-2">
                        <i class="fas fa-user-plus text-xl ml-4 md:ml-0"></i>
                        <span class="font-medium mx-auto md:mx-0">Add User</span>
                    </button>
                </div>
            </div>


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
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Year</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Email</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Usertype</th>
                                <th class="px-2 md:px-6 py-3 text-left text-xs md:text-base">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $userData): ?>
                                <tr class="border-t">
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['l_name'] . ', ' . $userData['f_name']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['student_id']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left">
                                        <?= $userData['yeartext'] ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['email']) ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left text-xs md:text-base">
                                        <?= htmlspecialchars($userData['usertype'] == 1 ? 'Officer' : 'Student') ?>
                                    </td>
                                    <td class="px-2 md:px-6 py-2 md:py-3 text-left whitespace-nowrap">
                                        <a href="#" id="user-<?= $userData['iduser'] ?>"
                                            class="edit-btn text-black-500 text-xs md:text-base mx-1 inline-flex items-center"
                                            data-id="<?= $userData['iduser'] ?>"
                                            data-lname="<?= htmlspecialchars($userData['l_name']) ?>"
                                            data-fname="<?= htmlspecialchars($userData['f_name']) ?>"
                                            data-email="<?= htmlspecialchars($userData['email']) ?>"
                                            data-year="<?= htmlspecialchars($userData['year']) ?>"
                                            data-usertype="<?= htmlspecialchars($userData['usertype']) ?>"
                                            data-student_id="<?= htmlspecialchars($userData['student_id']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <span class="text-gray-400 mx-1">|</span>
                                        <button type='button' class="cursor-pointer text-black text-xs md:text-base"
                                            onclick="openDeleteModal(<?= $userData['iduser'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($users)): ?>
            <div class="md:hidden space-y-4 mt-4">
                <?php foreach ($users as $userData): ?>
                    <div class="bg-white shadow-md border border-gray-200 rounded-lg p-5 flex justify-between items-center">
                        <div>
                            <p class="text-black-700 font-medium">
                                <?= htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']) ?>
                            </p>
                            <p class="font-bold text-sm <?= $userData['usertype'] == 1 ? 'text-black' : 'text-gray-500' ?>">
                                <?= $userData['student_id'] ?>
                            </p>
                            <p class="font-bold text-sm <?= $userData['usertype'] == 1 ? 'text-black' : 'text-gray-500' ?>">
                                Year <?= $userData['year'] ?>
                            </p>
                            <p class="text-gray-500 text-sm"><?= htmlspecialchars($userData['email']) ?></p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="#" class="edit-btn text-black text-xs md:text-base" data-id="<?= $userData['iduser'] ?>"
                                data-id="<?= $userData['iduser'] ?>" data-lname="<?= htmlspecialchars($userData['l_name']) ?>"
                                data-fname="<?= htmlspecialchars($userData['f_name']) ?>"
                                data-email="<?= htmlspecialchars($userData['email']) ?>"
                                data-year="<?= htmlspecialchars($userData['year']) ?>"
                                data-usertype="<?= htmlspecialchars($userData['usertype']) ?>"
                                data-student_id="<?= htmlspecialchars($userData['student_id']) ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type='button' class="text-black text-xs md:text-base"
                                onclick="openDeleteModal(<?= $userData['iduser'] ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 text-xs md:text-base">No users found.</p>
        <?php endif; ?>






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

    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center bg-[#00000078] invisible">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Add User</h2>
            <form id="addUserForm" action="./logic/user_add.php" method="POST">
                <input type="text" name="f_name" placeholder="First Name" required
                    class="w-full p-2 border rounded mb-2">
                <input type="text" name="l_name" placeholder="Last Name" required
                    class="w-full p-2 border rounded mb-2">

                <select name="year" id="year" required class="w-full p-2 border rounded mb-2 bg-white text-gray-700">
                    <option value="" selected disabled>Year</option>
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                    <option value="3">3rd</option>
                    <option value="4">4th</option>
                    <option value="0">Unknown</option>
                </select>

                <input type="email" name="email" placeholder="Email" pattern=".*@psu.palawan.edu.ph"
                    title="Email should be a PSU corporate one." required class="w-full p-2 border rounded mb-2">
                <input type="text" name="student_id" placeholder="Student ID"
                    pattern="20[0-9]{2}-[0-9]+-[0-9]{4}([A-Z]{2})?" title="Format: 20##-#-#### or 20##-#-####XX"
                    required class="w-full p-2 border rounded mb-2">

                <div class="mb-2 ">
                    <p class="font-semibold mb-1">User Type:</p>
                    <label class="inline-flex items-center mr-4">
                        <input type="radio" name="is_admin" value="1" class="form-radio text-black" required>
                        <span class="ml-2">Officer</span>
                    </label>
                    <label class="inline-flex ml-4 items-center">
                        <input type="radio" name="is_admin" checked value="0" class="form-radio text-black" required>
                        <span class="ml-2">Student</span>
                    </label>
                </div>

                <button type="submit" class="bg-black cursor-pointer text-white w-full py-2 rounded mt-2">Add
                    User</button>
            </form>
            <button id="cancelButton"
                class="bg-gray-400 cursor-pointer text-white w-full py-2 rounded mt-2">Cancel</button>
        </div>
    </div>


    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-[#00000078] invisible">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Edit User/Admin</h2>
            <form id="editForm" method="POST" action="./logic/user_admin_edit.php">
                <input type="hidden" name="user_id" id="edit_user_id">
                <label class="block text-gray-700">First Name:</label>
                <input type="text" name="f_name" id="edit_fname" class="border p-2 w-full rounded mb-2" required>

                <label class="block text-gray-700">Last Name:</label>
                <input type="text" name="l_name" id="edit_lname" class="border p-2 w-full rounded mb-2" required>


                <label class="block text-gray-700">Year Level:</label>
                <select name="year" id="edit_year" required
                    class="w-full p-2 border rounded mb-2 bg-white text-gray-700">
                    <option value="" disabled>Year</option>
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                    <option value="3">3rd</option>
                    <option value="4">4th</option>
                    <option value="0">Unknown</option>
                </select>


                <label class="block text-gray-700">Email:</label>
                <input type="email" name="email" id="edit_email" class="border p-2 w-full rounded mb-2"
                    pattern=".*@psu.palawan.edu.ph" title="Email should be a PSU corporate one." required>

                <label class="block text-gray-700">Student ID:</label>
                <input id="edit_student_id" name="student_id" class="w-full p-2 border rounded mb-2"
                    pattern="20[0-9]{2}-[0-9]+-[0-9]{4}([A-Z]{2})?" title="Format: 20##-#-#### or 20##-#-####XX"
                    required>

                <label class="block text-gray-700">New Password (leave blank to keep current):</label>
                <input type="password" name="password" id="edit_password" class="border p-2 w-full rounded mb-2">

                <!-- User Type Radio Buttons -->
                <div class="mb-4">
                    <p class="font-semibold mb-1">User Type:</p>
                    <label class="inline-flex items-center mr-4">
                        <input type="radio" name="is_admin" value="1" id="edit_usertype_officer"
                            class="form-radio text-black">
                        <span class="ml-2">Officer</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_admin" checked value="0" id="edit_usertype_student"
                            class="form-radio text-black">
                        <span class="ml-2">Student</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="submit" name="update_user"
                        class="bg-black cursor-pointer text-white px-4 py-2 rounded">Update</button>
                    <button type="button" id="closeModal"
                        class="bg-gray-400 cursor-pointer text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="fixed top-0 w-full h-full items-center justify-center bg-gray-600/40 backdrop-blur hidden">
        <div id="delete-main" class="w-10/12 md:w-1/4 bg-white rounded-lg flex flex-col px-4 py-2">
            <div class="py-2 font-semibold text-xl w-full border-b">Delete Item</div>
            <div class="w-full my-2 text-lg">
                <p>Are you sure to delete item <span id="delete-user" class="font-semibold"></span>?</p>
            </div>
            <input type="hidden" id="delete-iduser">

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
                <i class="fa-solid fa-circle-xmark text-7xl text-red-500"></i>
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
    <div id="loadingOverlay"
        class="fixed inset-0 z-50 bg-gray-300/50 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-2">
            <span class="animate-spin h-5 w-5 border-4 border-blue-500 border-t-transparent rounded-full"></span>
            <p class="text-lg font-semibold">Processing...</p>
        </div>
    </div>


    <script>
        function showLoading() {
            $('#loadingOverlay').removeClass('hidden');
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
                    if (response.status === 'success') {
                        localStorage.setItem("message", response.message)
                        location.reload();
                        localStorage.setItem("addSuccess", "true");
                    } else {
                        localStorage.setItem("message", response.message)
                        location.reload();
                        localStorage.setItem("addError", "true");
                    }
                },
                error: function (xhr, status, error) {
                    localStorage.setItem("message", response.message)
                    location.reload();
                    localStorage.setItem("addError", "true");
                }
            });
        }

        function updateUser() {
            let data = new FormData($('#editForm')[0]);

            $('#loader').addClass('flex').removeClass('hidden');

            $.ajax({
                url: 'logic/user_admin_edit.php',
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        localStorage.setItem("message", response.message)
                        localStorage.setItem("editSuccess", "true");
                        location.reload();
                    } else {
                        localStorage.setItem("message", response.message)
                        localStorage.setItem("editError", "true");
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    localStorage.setItem("message", response.message)
                    localStorage.setItem("editError", "true");
                    location.reload();
                }
            });
        }



        function hideSuccessMessage() {
            $('#success').addClass('hidden').removeClass('flex');
        }

        function hideErrorMessage() {
            $('#error').addClass('hidden').removeClass('flex');
        }

        function openDeleteModal(iduser) {
            let id = $('#user-' + iduser);
            let name = id.data('fname') + ' ' + id.data('lname');
            $('#deleteModal').removeClass('hidden').addClass('flex');
            $('#delete-user').text(name);
            $('#delete-iduser').val(iduser);
            $('body').addClass('overflow-y-hidden');
        }

        function closeDeleteModal() {
            $('#deleteModal').addClass('hidden').removeClass('flex');
            $('body').removeClass('overflow-y-hidden');
        }

        function confirmDelete() {
            let iduser = $('#delete-iduser').val();
            $('#loader').removeClass('hidden').addClass('flex');
            $.ajax({
                url: "logic/user_delete.php",
                method: 'POST',
                data: { iduser: iduser },
                dataType: 'json',
                // processData: false,
                // contentType: false,
                success: function (response) {
                    if (response.status == 'success') {
                        localStorage.setItem("message", response.message)
                        localStorage.setItem("deleteSuccess", "true");
                        location.reload();
                    } else {
                        localStorage.setItem("message", response.message)
                        localStorage.setItem("deleteError", "true");
                        location.reload();

                    }
                },
                error: function (xhr, status, error) {
                    localStorage.setItem("message", response.message)
                    location.reload();
                    localStorage.setItem("deleteError", "true");
                }
            })
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

            // Open Add User Modal
            document.getElementById("openAddUserModal")?.addEventListener("click", function () {
                console.log("Open Add User Modal clicked.");
                addUserModal.classList.remove("invisible");
            });


            // Edit buttons
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    console.log("Edit button clicked for ID:", this.dataset.id);

                    // Fill form fields
                    document.getElementById('edit_user_id').value = this.dataset.id;
                    document.getElementById('edit_lname').value = this.dataset.lname;
                    document.getElementById('edit_fname').value = this.dataset.fname;
                    document.getElementById('edit_student_id').value = this.dataset.student_id;
                    document.getElementById('edit_email').value = this.dataset.email;

                    // Set year
                    let yearSelect = document.getElementById('edit_year');
                    if (yearSelect) {
                        yearSelect.value = this.dataset.year || "";
                    }

                    // Set user type (radio buttons)
                    const officerRadio = document.getElementById('edit_usertype_officer');
                    const studentRadio = document.getElementById('edit_usertype_student');
                    if (officerRadio && studentRadio) {
                        if (this.dataset.usertype === "1") {
                            officerRadio.checked = true;
                        } else {
                            studentRadio.checked = true;
                        }
                    }

                    // Show modal
                    editModal.classList.remove('invisible');
                });
            });

            // Cancel Add User
            cancelButton?.addEventListener("click", function () {
                console.log("Cancel button clicked. Hiding modal...");
                addUserForm?.reset();
                addUserModal.classList.add("invisible");
            });

            // Close Edit Modal
            closeModal?.addEventListener("click", function () {
                console.log("Close Edit Modal clicked.");
                editModal.classList.add("invisible");
            });

            // Close modals when clicking outside
            window.addEventListener("click", (event) => {
                if (event.target === addUserModal) {
                    console.log("Clicked outside Add User Modal. Closing...");
                    addUserForm?.reset();
                    addUserModal.classList.add("invisible");
                }
                if (event.target === editModal) {
                    console.log("Clicked outside Edit Modal. Closing...");
                    editModal.classList.add("invisible");
                }
            });
        });

        $(document).ready(function () {
            $('#header-title').text('Users');
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

            if (localStorage.getItem('editSuccess') === 'true') {
                message = localStorage.getItem('message');
                $('#success').addClass('flex').removeClass('hidden');
                $('#success-message').text(message);
                localStorage.removeItem('editSuccess');
                localStorage.removeItem('message');
            }

            if (localStorage.getItem('editError') === 'true') {
                message = localStorage.getItem('message');
                $('#error-message').text(message);
                $('#error').addClass('flex').removeClass('hidden');
                localStorage.removeItem('editError');
                localStorage.removeItem('message');
            }

            if (localStorage.getItem('deleteSuccess') === 'true') {
                message = localStorage.getItem('message');
                $('#success-message').text(message);
                $('#success').addClass('flex').removeClass('hidden');
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


            $('#addUserForm').on('submit', function (e) {
                e.preventDefault();
                addUser();
            })

            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                updateUser();
            });

            $(document).on('click', function () {

                if ($(event.target).closest('#deleteModal').length && !$(event.target).closest('#delete-main').length) {
                    closeDeleteModal();
                }
            })

        })


        function getSelectedValues(form) {
            showLoading();
            const data = new FormData(form);
            const params = new URLSearchParams();

            // Search value
            const search = document.getElementById("userSearch").value;
            if (search) {
                params.set('search', search);
            }

            // Collect filter checkboxes
            for (const pair of data.entries()) {
                params.append(pair[0], pair[1]);
            }

            window.location.search = params.toString(); // Reload with query params
        }

        document.getElementById("filterForm").addEventListener("submit", function (e) {
            e.preventDefault();
            getSelectedValues(this);
        });

        document.getElementById("toggleFilterMenu").addEventListener("click", () => {
            const menu = document.getElementById("filterMenu");
            menu.classList.toggle("hidden");
        });

        let debounceTimer;
        document.getElementById("userSearch").addEventListener("keyup", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                getSelectedValues(document.getElementById("filterForm"));
            }, 300); // Adjust delay as needed
        });

        $(document).ready(function () {
            $(document).on('click', function () {
                if (!$(event.target).closest("#filterMenu").length && !$(event.target).closest("#toggleFilterMenu").length) {
                    $("#filterMenu").addClass('hidden');
                }
            })
        })
    </script>


</body>

</html>