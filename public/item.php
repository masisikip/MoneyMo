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
    <aside class="w-64 bg-black text-white p-6 overflow-y-auto fixed h-full">
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
    </aside>

    <div class="flex-1 p-8 ml-64">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>Item Management</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
        </div>

        <div class="bg-white shadow p-4 rounded-lg">
        
            <!-- Merch Category -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2 flex items-center">
                    Merch
                    <button class="ml-4 px-4 py-2 text-black rounded">
                        <i class="fas fa-plus"></i>
                    </button>
                </h3>
                <hr class="border-gray-300 mb-4">
                <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>ID Lanyard <span class="text-gray-600 float-right">200 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Organization Shirt <span class="text-gray-600 float-right">550 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Pins <span class="text-gray-600 float-right">50 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Mugs <span class="text-gray-600 float-right">100 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Stickers <span class="text-gray-600 float-right">5 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collection Category -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2 flex items-center">
                    Collection
                    <button class="ml-4 px-4 py-2 text-black rounded">
                        <i class="fas fa-plus"></i>
                    </button>
                </h3>
                <hr class="border-gray-300 mb-4">
                <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Organization Fee <span class="text-gray-600 float-right">150 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Organization Fines <span class="text-gray-600 float-right">0 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Miscellaneous Category -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2 flex items-center">
                    Miscellaneous
                    <button class="ml-4 px-4 py-2 text-black rounded">
                        <i class="fas fa-plus"></i>
                    </button>
                </h3>
                <hr class="border-gray-300 mb-4">
                <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Acquaintance Fee <span class="text-gray-600 float-right">550 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Category -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2 flex items-center">
                    Services
                    <button class="ml-4 px-4 py-2 text-black rounded">
                        <i class="fas fa-plus"></i>
                    </button>
                </h3>
                <hr class="border-gray-300 mb-4">
                <div class="grid justify-center w-full grid-cols-1 gap-4 p-4 mt-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:mt-9">
                    <div class="p-4 bg-gray-200 rounded shadow h-48 flex flex-col justify-between">
                        <p>Printing <span class="text-gray-600 float-right">1 PHP</span></p>
                        <div class="flex justify-end space-x-2">
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="px-3 py-1 text-black rounded">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
