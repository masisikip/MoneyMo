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

    <div class="flex-1 p-8">
        <div class="bg-white shadow p-4 rounded-lg mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold flex items-center">
                <span>Item Management</span>
                <span class="text-gray-500 mx-2">|</span>
                <span class="text-gray-600 font-normal">Hello, admin Jamaica!</span>
            </h1>
        </div>
    </div>

</body>
</html>