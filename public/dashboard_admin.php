<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col md:flex-row h-screen bg-gray-100 overflow-hidden">
    <div class="md:hidden bg-black text-white p-4 flex justify-start items-center">
        <button id="menuToggle" class="text-white focus:outline-none mr-4">
            <i class="fas fa-bars text-2xl"></i>
        </button>
        <div class="flex items-center">
            <img src="assets/logo.png" alt="MoneyMo Logo" class="h-8 w-8 mr-2">
            <h2 class="text-xl font-bold">MoneyMo</h2>
        </div>
    </div>

    <aside id="sidebar" class="hidden md:block w-full md:w-64 bg-black text-white p-6 h-screen md:h-auto overflow-y-auto transition-all duration-300 ease-in-out">
        <div class="hidden md:flex items-center mb-10 mt-6">
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

    <div class="flex-1 p-4 md:p-8 overflow-y-auto">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between">
            <h1 class="text-xl md:text-2xl font-bold flex flex-col sm:flex-row sm:items-center">
                <span>Dashboard</span>
                <span class="hidden sm:inline text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
        </div>

        <div class="bg-white shadow rounded-lg p-4 md:p-6 overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-2 md:px-4 text-left text-xs md:text-sm">
                            Student Name
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </th>
                        <th class="py-2 px-2 md:px-4 text-left text-xs md:text-sm">
                            Item Category
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </th>
                        <th class="py-2 px-2 md:px-4 text-left text-xs md:text-sm">
                            Price
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </th>
                        <th class="py-2 px-2 md:px-4 text-left text-xs md:text-sm">Option</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Dela Cruz, Juan</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Iphone 16</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">499 USD</td>
                        <td class="py-2 md:py-3 px-2 md:px-4">
                            <button class="bg-black text-white px-2 md:px-4 py-1 rounded-lg text-xs md:text-sm">Print</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Ponce, Crissel</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Org Shirt</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">500 USD</td>
                        <td class="py-2 md:py-3 px-2 md:px-4">
                            <button class="bg-black text-white px-2 md:px-4 py-1 rounded-lg text-xs md:text-sm">Print</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Pineda, Jr. Fernando</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">Org Fee</td>
                        <td class="py-2 md:py-3 px-2 md:px-4 text-xs md:text-base">150 USD</td>
                        <td class="py-2 md:py-3 px-2 md:px-4">
                            <button class="bg-black text-white px-2 md:px-4 py-1 rounded-lg text-xs md:text-sm">Print</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<!--Pagination-->
<div class="absolute bottom-10 left-0 w-full flex justify-center">
    <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">Previous</button>
    <div class="flex space-x-2 mx-4">
        <button class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-500">1</button>
        <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">2</button>
        <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">3</button>
    </div>
    <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">Next</button>
</div>


</body>
</html>