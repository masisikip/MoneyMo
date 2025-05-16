<?php
include_once '../../includes/connect-db.php';
include_once '../../includes/token.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload) {
        $iduser = $payload['user_id'];
    }
}
$stmt = $pdo->prepare(query: "SELECT * FROM user WHERE iduser = ?");
$stmt->execute([$iduser]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MoneyMo - Profile</title>
    <link rel="stylesheet" href="../../css/styles.css" />
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include_once '../../includes/partial.php'; ?>

    <div id="message"
        class="mb-4 hidden w-96 absolute top-32 left-1/2 transform -translate-x-1/2 p-3 rounded text-center z-50">
    </div>

    <main class="mt-32 md:mx-0 mx-4 flex items-center justify-center">
        <div class="w-full max-w-md p-8 border rounded-lg shadow-lg">

            <h1 class="text-3xl font-bold mb-6 text-center">My Profile</h1>

            <div class="mb-4">
                <p><span class="font-semibold">Full Name:</span>
                    <?= htmlspecialchars($user['f_name'] . ' ' . $user['l_name']) ?></p>
                <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($user['email']) ?></p>
                <p><span class="font-semibold">Student ID:</span> <?= htmlspecialchars($user['student_id']) ?></p>
                <p><span class="font-semibold">Year:</span> <?= htmlspecialchars($user['year']) ?></p>

                <div class="inline-flex items-center justify-center w-full">
                    <hr class="w-full h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                    <span
                        class="absolute px-3 font-medium text-gray-900 -translate-x-1/2 bg-white left-1/2 dark:text-white dark:bg-gray-900">change
                        password</span>
                </div>

                <input type="password" id="oldPass" class="w-full border p-2 rounded mb-4" placeholder="Old Password">
                <input type="password" id="newPass" class="w-full border p-2 rounded mb-4" placeholder="New Password">
                <input type="password" id="confirmPass" class="w-full border p-2 rounded mb-4"
                    placeholder="Confirn Password">

                <button id="submitPass"
                    class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 mb-2 flex items-center justify-center gap-2 cursor-pointer">
                    <span id="submitSpan">Update Password</span>
                    <span id="loader"
                        class="hidden border-2 border-white border-t-transparent rounded-full w-4 h-4 animate-spin"></span>
                </button>
            </div>
        </div>



    </main>



    <script>
        $(document).ready(function () {
            $('#header-title').text('Profile');

            function isStrongPassword(pw) {
                if (pw.length < 8) {
                    showMessage('Password must be at least 8 characters long', 'error');
                    return false;
                }
                if (!/[A-Z]/.test(pw)) {
                    showMessage('Password must contain at least 1 uppercase letter', 'error');
                    return false;
                }
                if (!/[a-z]/.test(pw)) {
                    showMessage('Password must contain at least 1 lowercase letter', 'error');
                    return false;
                }
                if (!/[0-9]/.test(pw)) {
                    showMessage('Password must contain at least 1 number', 'error');
                    return false;
                }
                if (!/[\W_]/.test(pw)) {
                    showMessage('Password must contain at least 1 special character', 'error');
                    return false;
                }
                return true;
            }

            function showMessage(msg, type) {
                let bg = (type === 'success')
                    ? 'bg-green-100 text-green-700 border border-green-400'
                    : 'bg-red-100 text-red-700 border border-red-400';

                $('#message')
                    .attr('class', `mb-4 w-96 fixed top-32 left-1/2 transform -translate-x-1/2 p-3 rounded text-center z-50 ${bg}`)
                    .text(msg)
                    .removeClass('hidden');

                setTimeout(() => {
                    $('#message').addClass('hidden');
                }, 3000);
            }

            function submitPasswordUpdate() {
                let oldPass = $('#oldPass').val().trim();
                let newPass = $('#newPass').val().trim();
                let confirmPass = $('#confirmPass').val().trim();

                if (oldPass === '' || newPass === '' || confirmPass === '') {
                    showMessage('All fields are required', 'error');
                    return;
                }

                if (newPass !== confirmPass) {
                    showMessage('Passwords do not match', 'error');
                    return;
                }

                if (!isStrongPassword(newPass)) {
                    return;
                }

                $('#loader').removeClass('hidden');
                $('#submitSpan').addClass('hidden');


                $.ajax({
                    url: 'logic/update_password.php',
                    type: 'POST',
                    data: { old_password: oldPass, new_password: newPass },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            showMessage(res.message, 'success');
                            $('#oldPass').val('');
                            $('#newPass').val('');
                            $('#confirmPass').val('');
                        } else {
                            showMessage(res.message, 'error');
                        }
                    },
                    error: function () {
                        showMessage('An unexpected error occurred', 'error');
                    },
                    complete: function () {
                        $('#loader').addClass('hidden');
                        $('#submitSpan').removeClass('hidden');

                    }
                });
            }

            $('#submitPass').click(submitPasswordUpdate);

            $('#oldPass, #newPass, #confirmPass').keypress(function (e) {
                if (e.which === 13) {
                    submitPasswordUpdate();
                }
            });

        });
    </script>



</body>

</html>