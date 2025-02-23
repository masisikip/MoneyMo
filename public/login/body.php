<main class="flex h-screen">
    <div class="w-2/3 bg-white flex items-center justify-center">
        <div class="max-w-md w-full">
            <div class="absolute top-4 left-4 flex items-center mb-8">
                <img src="logo.png" alt="logo" class="h-8 w-8 mr-2">
                <h1 class="text-2xl font-bold">MoneyMo</h1>
            </div>

            <h2 class="text-3xl font-bold mb-2">Login to Your Account</h2>
            <p class="text-gray-500 mb-6">Nigga</p>

            <form id="loginForm" class="space-y-4">
                <input type="email" id="email" placeholder="Enter your email" class="w-full p-3 rounded-lg bg-gray-200 focus:outline-none">

                <div class="relative">
                    <input type="password" id="password" placeholder="Enter your password" class="w-full p-3 rounded-lg bg-gray-200 focus:outline-none">
                    <span class="absolute right-4 top-3 cursor-pointer">👁️</span>
                </div>

                <button type="submit" class="w-full bg-black text-white p-3 rounded-lg hover:bg-gray-800">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Right Section: Sign Up -->
    <div class="w-1/3 bg-black text-white flex flex-col items-center justify-center">
        <button class="absolute top-4 right-4 text-xl">&times;</button>
        <h2 class="text-2xl font-bold mb-4">New Here?</h2>
        <p class="text-center mb-6">Nigga</p>
        <button class="bg-white text-black px-6 py-2 rounded-lg hover:bg-gray-300">Sign Up</button>
    </div>
</main>
