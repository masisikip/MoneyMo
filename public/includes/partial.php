<?php
session_start();
require_once 'token.php';

$usertype = 0;
if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload && isset($payload['user_type'])) {
        $usertype = $payload['user_type'];
    }
}

$host = $_SERVER['HTTP_HOST'];

if ($host === 'localhost') {
    $path = $_SERVER['REQUEST_SCHEME'] . '://' . $host . '/MoneyMo/public/';
} else {
    $path = $_SERVER['REQUEST_SCHEME'] . '://' . $host . '/';
}
?>

<header
    class="<?php echo ($usertype == 1) ? 'bg-white text-black' : 'bg-black text-white'; ?> shadow-md p-4 flex justify-between items-center">
    <div class="flex items-center space-x-4 ">

    <?php if ($usertype == 1) { ?>
        <button id="toggleSidebar" class="p-2 focus:outline-none cursor-pointer">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    <?php } ?>


        <div class="flex items-center space-x-2">
            <h1 class="text-xl font-bold">DASHBOARD</h1>
            <span class="<?php echo ($usertype == 1) ? 'text-gray-600' : 'text-gray-300'; ?>">
                | Hello, <?php echo ($usertype == 1) ? 'Admin' : 'User'; ?> <?= $payload['user_name']; ?>
            </span>
        </div>
    </div>

    <?php if ($usertype == 0) { ?>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <button id="toggleMenu" class="p-2 cursor-pointer focus:outline-none">
                    <i class="fas fa-ellipsis-v text-2xl"></i>
                </button>

                <div id="menuDropdown" class="hidden absolute bg-white shadow-lg right-0 mt-2 w-40 rounded-lg">
                    <a href="<?= $path . 'user/"' ?>
                        class=" block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-house"></i> Home
                    </a>
                    <a href="<?= $path . 'user/qr' ?>" class="block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-qrcode"></i> QR Code
                    </a>
                    <a href="<?= $path . 'user/profile' ?>" class="block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-user"></i> Profile
                    </a>

                    <a href="<?= $path . 'logout.php' ?>" class="block px-4 py-2 hover:bg-gray-200 text-red-500 rounded-lg">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
</header>


<div id="sidebarOverlay" class="hidden fixed inset-0 bg-gray-100/50 z-40"></div>

<aside id="sidebar"
    class="fixed left-0 top-0 w-64 h-screen bg-black text-white p-6 transform -translate-x-full transition-transform z-50 shadow-lg">
    <h2 class="text-2xl font-bold mb-6">MoneyMo</h2>
    <nav>
        <ul class="space-y-4">
            <li><a href="<?= $path . 'admin/' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-home"></i>
                    <span>Dashboard</span></a></li>
            <li><a href="<?= $path . 'admin/profile' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-user"></i>
                    <span>Profile</span></a></li>
            <li><a href="<?= $path . 'admin/inventory' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-box"></i>
                    <span>Inventory</span></a></li>
            <li><a href="<?= $path . 'admin/scanner' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-qrcode"></i>
                    <span>QR Scanner</span></a></li>
            <li><a href="<?= $path . 'admin/code' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-barcode"></i>
                    <span>QR Code</span></a></li>
            <li><a href="<?= $path . 'admin/item' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-list"></i>
                    <span>Items Management</span></a></li>
            <li><a href="<?= $path . 'admin/users' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded"><i class="fas fa-users"></i>
                    <span>Users Management</span></a></li>
            <li><a href="<?= $path . 'logout.php' ?>"
                    class="flex items-center space-x-2 hover:bg-red-500 p-2 rounded"><i class="fas fa-sign-out-alt"></i>
                    <span>Log out</span></a></li>
        </ul>
    </nav>
</aside>


<script>
    $(document).ready(function () {
        $("#toggleSidebar").click(function () {
            $("#sidebar").toggleClass("-translate-x-full");
            $("#sidebarOverlay").toggleClass("hidden");
        });

        $("#sidebarOverlay").click(function () {
            $("#sidebar").addClass("-translate-x-full");
            $(this).addClass("hidden");
        });

        $("#toggleMenu").click(function () {
            $("#menuDropdown").toggleClass("hidden");
        });

        $(document).click(function (event) {
            if (!$(event.target).closest("#toggleMenu, #menuDropdown").length) {
                $("#menuDropdown").addClass("hidden");
            }
        });
    });
</script>