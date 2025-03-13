<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css"></link>
    <script src="https://kit.fontawesome.com/YOUR-FONT-AWESOME-KEY.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="flex flex-col h-screen bg-gray-100 p-6">
    

    <div class="bg-white shadow p-4 rounded-lg mb-6">
        <h1 class="text-2xl font-bold">Inventory Records</h1>
        <p class="text-gray-600">Hello, admin Jamaica!</p>
    </div>
    

    <div class="bg-white flex flex-col md:flex-row items-center justify-between rounded-xl overflow-hidden shadow-sm p-4 mb-4 space-y-3 md:space-y-0">
        <div class="flex items-center w-full md:max-w-2xl">
            <span class="pl-3 pr-2 text-gray-500">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="Search inventory..." 
                class="p-2 w-full border-gray-300 focus:ring-2 focus:outline-none focus:ring-white">
        </div>
        <div class="flex items-center space-x-2 md:space-x-4 w-full md:w-auto">
            <select class="p-2 border rounded-lg w-full md:w-auto">
                <option>Filter by</option>
                <option>Unclaimed</option>
            </select>
            <button class="text-gray-500 hover:text-black">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>
    </div>
    
    <!-- Desktop View -->
    <div class="hidden md:block bg-white shadow rounded-lg p-6 mb-4 overflow-x-auto">
        <table class="w-full border-collapse overflow-hidden rounded-lg min-w-[600px]">
            <thead class="bg-gray-200 text-gray-700 rounded-t-lg">
                <tr class="bg-gray-200 text-gray-700">
                    <th class="py-2 px-4 text-left">Item Name <i class="fas fa-sort ml-2 text-gray-400"></i></th>
                    <th class="py-2 px-4 text-left">Purchased By <i class="fas fa-sort ml-2 text-gray-400"></i></th>
                    <th class="py-2 px-4 text-left">Date of Purchase <i class="fas fa-sort ml-2 text-gray-400"></i></th>
                    <th class="py-2 px-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="py-3 px-4">Org Shirt</td>
                    <td class="py-3 px-4">De La Cruz, Juan</td>
                    <td class="py-3 px-4">02-23-25</td>
                    <td class="py-3 px-4">
                        <button class="bg-gray-300 text-black px-4 py-1 rounded-full hover:bg-black hover:text-white min-w-[120px]">Claimed</button>
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 px-4">Lanyard</td>
                    <td class="py-3 px-4">Ponce, Crishel</td>
                    <td class="py-3 px-4">02-24-25</td>
                    <td class="py-3 px-4">
                        <button class="bg-black text-white px-4 py-1 rounded-full hover:bg-gray-500 min-w-[120px]">Unclaimed</button>
                    </td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 px-4">Lanyard</td>
                    <td class="py-3 px-4">Ala Christian</td>
                    <td class="py-3 px-4">02-24-25</td>
                    <td class="py-3 px-4">
                        <button class="bg-gray-300 text-black px-4 py-1 rounded-full hover:bg-black hover:text-white min-w-[120px]">Claimed</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Mobile View -->
    <div class="md:hidden space-y-4">
        <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-bold">Lanyard</p>
                <p class="text-gray-600">Ponce, Crishel</p>
                <p class="text-gray-500 text-sm">02-24-25</p>
            </div>
            <button class="bg-black text-white px-4 py-1 rounded-full min-w-[120px]">Claimed</button>
        </div>
        <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-bold">Org Shirt</p>
                <p class="text-gray-600">De La Cruz, Juan</p>
                <p class="text-gray-500 text-sm">02-23-25</p>
            </div>
            <button class="bg-gray-300 text-black px-4 py-1 rounded-full min-w-[120px]">Unclaimed</button>
        </div>
    </div>
    
 
    <div class="flex justify-center mt-4">
        <div class="flex items-center space-x-2">
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&lt;</button>
            <button class="bg-black text-white px-3 py-2 rounded-lg hover:bg-gray-500">1</button>
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">2</button>
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">...</button>
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">9</button>
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">10</button>
            <button class="bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400">&gt;</button>
        </div>
    </div>

</body>
</html>