<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'token.php';

$usertype = 0;
$username = 'Guest';
if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload && isset($payload['user_type'])) {
        $usertype = $payload['user_type'];
        $username = $payload['user_name'] ?? 'User';
    }
}

$host = $_SERVER['HTTP_HOST'];
$scheme = $_SERVER['REQUEST_SCHEME'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http');
$path = ($host === 'localhost')
    ? $scheme . '://' . $host . '/MoneyMo/public/'
    : $scheme . '://' . $host . '/';
?>

<header
    class="<?= $usertype == 1 ? 'bg-base-300 text-black' : 'bg-base-300 text-white' ?> shadow-md p-4 flex justify-between items-center w-full top-0">
    <div class="flex items-center space-x-4">
        <?php if ($usertype == 1): ?>
            <button id="toggleSidebar" class="p-2 focus:outline-none cursor-pointer">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        <?php endif; ?>

        <div class="flex items-center space-x-2">
            <img  src="<?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ?>/MoneyMo/public/assets/logo-circle.png"  alt="MoneyMo Logo" class="h-10 w-10 object-contain" />
            <h1 id="header-title" class="md:text-xl text-lg font-bold"></h1>
            <span class="<?= $usertype == 1 ? 'text-gray-600' : 'text-gray-300' ?> md:text-base text-sm">
                | Hello, <?= $usertype == 1 ? 'Admin' : '' ?> <?= htmlspecialchars($username) ?>
            </span>
        </div>
    </div>

    <div class="flex items-center justify-between space-x-4">
        <div class="text-center">
            <p id="time" class="md:text-xl text-sm m md:px-10 md:mx-10 font-mono text-blue-600">Loading...</p>
        </div>

        <?php if ($usertype == 0): ?>
            <div class="relative">
                <button id="toggleMenu" class="p-2 cursor-pointer focus:outline-none">
                    <i class="fas fa-ellipsis-v text-2xl"></i>
                </button>

                <div id="menuDropdown" class="hidden absolute bg-white shadow-lg right-0 mt-2 w-40 rounded-lg z-50">
                    <a href="<?= $path ?>user/" class="block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-house"></i> Home
                    </a>
                    <a href="<?= $path ?>user/qr" class="block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-qrcode"></i> QR Code
                    </a>
                    <a href="<?= $path ?>user/profile" class="block px-4 py-2 text-black hover:bg-gray-200 rounded-lg">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="<?= $path ?>logout.php" class="block px-4 py-2 hover:bg-gray-200 text-red-500 rounded-lg">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>

<div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/50 z-40 backdrop-blur-sm"></div>

<aside id="sidebar"
    class="fixed left-0 top-0 w-64 h-screen bg-base-300 text-white p-6 transform -translate-x-full transition-transform duration-300 z-50 shadow-lg">
    <h2 class="text-2xl font-bold mb-6">MoneyMo</h2>
    <nav>
        <ul class="space-y-3 text-sm">
            <li><a href="<?= $path ?>admin/" class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-home"></i><span>Dashboard</span></a></li>

            <li><a href="<?= $path . 'admin/collection' ?>"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-money-bill-1-wave"></i>
                    <span>Collection</span></a></li>
            <li><a href="<?= $path ?>admin/inventory"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-box"></i><span>Inventory</span></a></li>
            <li><a href="<?= $path ?>admin/statistics"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-chart-line"></i><span>Statistics</span></a></li>
            <li><a href="<?= $path ?>admin/scanner"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-qrcode"></i><span>QR Scanner</span></a></li>

            <li><a href="<?= $path ?>admin/item"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-list"></i><span>Items Management</span></a></li>
            <li><a href="<?= $path ?>admin/users"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-users"></i><span>Users Management</span></a></li>
            <li><a href="<?= $path ?>admin/import"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-import"></i><span>Import Students</span></a></li>
            <li><a href="<?= $path ?>admin/export"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-arrow-up"></i><span>Export Collection</span></a></li>
            <hr>
            <li><a href="<?= $path ?>user/" class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-invoice-dollar"></i><span>My Purchases</span></a></li>
            <li><a href="<?= $path ?>admin/code"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fas fa-barcode"></i><span>QR Code</span></a></li>
            <li><a href="<?= $path ?>user/profile"
                    class="flex items-center space-x-2 hover:bg-gray-700 px-2 py-1 rounded"><i
                        class="fa-solid fa-user"></i><span>Profile</span></a></li>
            <hr>
            <li><a href="<?= $path ?>logout.php"
                    class="flex items-center space-x-2 hover:bg-red-500 px-2 py-1 rounded"><i
                        class="fas fa-sign-out-alt"></i><span>Log out</span></a></li>
        </ul>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sidebar toggle
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            });
        }

        // Close sidebar when clicking outside
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // User menu toggle
        const toggleMenu = document.getElementById('toggleMenu');
        const menuDropdown = document.getElementById('menuDropdown');

        if (toggleMenu) {
            toggleMenu.addEventListener('click', (e) => {
                e.stopPropagation();
                menuDropdown.classList.toggle('hidden');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!menuDropdown.contains(e.target) && !toggleMenu.contains(e.target)) {
                    menuDropdown.classList.add('hidden');
                }
            });
        }
    });




    function updateTime() {
        let now = new Date();
        let utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        let phTime = new Date(utc + (3600000 * 8)); // UTC+8

        let hours = phTime.getHours().toString().padStart(2, '0');
        let minutes = phTime.getMinutes().toString().padStart(2, '0');
        let seconds = phTime.getSeconds().toString().padStart(2, '0');

        let timeString = `${hours}:${minutes}:${seconds}`;
        $('#time').text(timeString);
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>
