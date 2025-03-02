<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex h-screen bg-gray-100">
    <!-- <aside class="w-64 bg-black text-white p-6">
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
    </aside> -->

    <div class="flex-1 p-8">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>Inventory Records</span>
                <span class="text-gray-500 mx-3">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
        </div>


        <div class="bg-white shadow rounded-lg p-6">

            <div class="bg-white flex items-center rounded-xl overflow-hidden shadow-lg mb-4 w-fit">
                <span class="pl-3 pr-2 text-gray-500">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Search inventory..." 
                class="p-2 w-56 border-gray-300 focus:ring-2 focus:outline-none focus:ring-white">
                <button class="bg-black text-white px-4 py-2 rounded-r hover:bg-gray-500">Search</button>
            </div>


            <table class="w-full border-collapse overflow-hidden rounded-lg">
                <thead class="bg-gray-200 text-gray-700 rounded-t-lg">
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 text-left">
                            Item Name
                            <i class="fas fa-sort ml-2 text-gray-400"></i> 
                        </th>
                        <th class="py-2 px-4 text-left">
                            Purchased By
                            <i class="fas fa-sort ml-2 text-gray-400"></i> 
                        </th>
                        <th class="py-2 px-4 text-left">
                            Date of Purchase
                            <i class="fas fa-sort ml-2 text-gray-400"></i> 
                        </th>
                        <th class="py-2 px-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-3 px-4">Org Shirt</td>
                        <td class="py-3 px-4">De La Cruz, Juan</td>
                        <td class="py-3 px-4">02-23-25</td>
                        <td class="py-3 px-4">
                            <button class="bg-gray-300 text-black px-4 py-1 rounded-full hover:bg-black hover:text-white min-w-[105px]">Claimed</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 px-4">Lanyard</td>
                        <td class="py-3 px-4">Ponce, Crishel</td>
                        <td class="py-3 px-4">02-24-25</td>
                        <td class="py-3 px-4">
                            <button class="bg-black text-white px-4 py-1 rounded-full hover:bg-gray-500 min-w-[100px]">Unclaimed</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 px-4">Lanyard</td>
                        <td class="py-3 px-4">Ala Christian</td>
                        <td class="py-3 px-4">02-24-25</td>
                        <td class="py-3 px-4">
                            <button class="bg-gray-300 text-black px-4 py-1 rounded-full hover:bg-black hover:text-white min-w-[105px]">Claimed</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
           
        
        <!-- Pagination -->         
         <div class="flex justify-center mt-6">
            <div class="flex space-x-2">
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400"><</button>
            <button class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-500">1</button>
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">2</button>
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">...</button>
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">9</button>
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">10</button>
            <button class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">></button>
        </div>

    </div>
        </div>

</body>
</html>