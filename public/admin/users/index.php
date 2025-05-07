<?php
include_once '../../includes/connect-db.php';


$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$whereClause = "WHERE 1 = 1";


if (!empty($search)) {
    $whereClause .= " AND (email LIKE :search1 OR f_name LIKE :search2 OR l_name LIKE :search3 OR student_id LIKE :search4 )";
}
if ($filter === 'officer') {
    $whereClause .= " AND usertype = 1";
}
if ($filter === 'student') {
    $whereClause .= " AND usertype = 0";
}

$countQuery = "
SELECT COUNT(*) 
FROM user
$whereClause
";

$countStmt = $pdo->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $countStmt->bindValue(':search1', $searchTerm);
    $countStmt->bindValue(':search2', $searchTerm);
    $countStmt->bindValue(':search3', $searchTerm);
    $countStmt->bindValue(':search4', $searchTerm);
}

$countStmt->execute();
$total_records = $countStmt->fetchColumn();

$total_pages = ceil($total_records / $limit);


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
LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($query);

$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $stmt->bindValue(':search1', $searchTerm);
    $stmt->bindValue(':search2', $searchTerm);
    $stmt->bindValue(':search3', $searchTerm);
    $stmt->bindValue(':search4', $searchTerm);
}

$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <button id="openAddUserModal"
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
                                        <a href="#"
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
                            <p class="text-black-700 font-medium"><?= htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']) ?></p>
                            <p class="font-bold text-sm <?= $userData['usertype'] == 1 ? 'text-black' : 'text-gray-500' ?>"><?= $userData['student_id'] ?></p>
                            <p class="font-bold text-sm <?= $userData['usertype'] == 1 ? 'text-black' : 'text-gray-500' ?>"><?= $userData['year'] ?></p>
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
                <input type="text" name="l_name" placeholder="Last Name" required
                    class="w-full p-2 border rounded mb-2">
                <input type="text" name="f_name" placeholder="First Name" required
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

                <button type="submit" class="bg-black cursor-pointer text-white w-full py-2 rounded mt-2">Add User</button>
            </form>
            <button id="cancelButton" class="bg-gray-400 cursor-pointer text-white w-full py-2 rounded mt-2">Cancel</button>
        </div>
    </div>


    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-[#00000078] invisible">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Edit User/Admin</h2>
            <form id="editForm" method="POST" action="./logic/user_admin_edit.php">
                <input type="hidden" name="user_id" id="edit_user_id">

                <label class="block text-gray-700">Last Name:</label>
                <input type="text" name="l_name" id="edit_lname" class="border p-2 w-full rounded mb-2" required>

                <label class="block text-gray-700">First Name:</label>
                <input type="text" name="f_name" id="edit_fname" class="border p-2 w-full rounded mb-2" required>

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
                        <input type="radio" name="usertype" value="1" id="edit_usertype_officer"
                            class="form-radio text-black">
                        <span class="ml-2">Officer</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="usertype" checked value="0" id="edit_usertype_student"
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

    <script>

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
    </script>


</body>

</html>