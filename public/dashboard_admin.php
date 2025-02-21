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
    <aside class="w-64 bg-black text-white p-6">
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
                    <a href="items.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
                        <i class="fas fa-cubes mr-2"></i> Item Management
                    </a>
                </li>
                <li>
                    <a href="users.php" class="flex items-center p-3 hover:bg-[#545454] rounded">
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

    <div class="flex-1 p-8">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>Dashboard</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 text-left">
                            Student Name
                            <i class="fas fa-sort ml-2 text-gray-400"></i> <!-- Sort Arrow -->
                        </th>
                        <th class="py-2 px-4 text-left">
                            Item Category
                            <i class="fas fa-sort ml-2 text-gray-400"></i> <!-- Sort Arrow -->
                        </th>
                        <th class="py-2 px-4 text-left">
                            Price
                            <i class="fas fa-sort ml-2 text-gray-400"></i> <!-- Sort Arrow -->
                        </th>
                        <th class="py-2 px-4 text-left">Option</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-3 px-4">Dela Cruz, Juan</td>
                        <td class="py-3 px-4">Iphone 16</td>
                        <td class="py-3 px-4">499 USD</td>
                        <td class="py-3 px-4">
                            <button class="bg-black text-white px-4 py-1 rounded-lg">Print</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 px-4">Ponce, Crissel</td>
                        <td class="py-3 px-4">Org Shirt</td>
                        <td class="py-3 px-4">500 USD</td>
                        <td class="py-3 px-4">
                            <button class="bg-black text-white px-4 py-1 rounded-lg">Print</button>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 px-4">Pineda, Jr. Fernando</td>
                        <td class="py-3 px-4">Org Fee</td>
                        <td class="py-3 px-4">150 USD</td>
                        <td class="py-3 px-4">
                            <button class="bg-black text-white px-4 py-1 rounded-lg">Print</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>