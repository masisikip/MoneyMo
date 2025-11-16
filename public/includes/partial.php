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
          <svg class="text-primary w-15 h-15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 304 318" width="304" height="318">
            <path fill="currentColor" d="M173 49h3q10-1 18 3l3 1q24 9 35 33l1 2a67 67 0 0 1-11 61l-4 6h31l1 7v12l-1 4h-51l3 15v11q-1 30-23 50-9 8-21 11l-3 1c-14 5-32 4-46-2l-2-2-8-4v-2h-3l-5-5v-2h-2a67 67 0 0 1-14-42v-2q0-19 11-33l2-2 1-4-36-1 1-24h58l-2-13v-17q0-22 15-40l2-3 7-6h2l1-3 7-4 2-1q13-6 28-5m-17 25-1 2-3 1v2h-2q-4 2-6 5v3l-2 1q-5 4-6 10l-1 2q-4 14 0 28v8l1 3q0 12-7 20l-2 5h-2v2h-2l-4 4-3 4c-7 6-7 6-10 14l-2 1q-8 18-2 38l3 5h2l1 2q3 6 9 9l6-2q2-4 1-9v-7l11 2 1-4h-5l-1-3-5-1v-14l8 2h2l9 3 1-4q-8-5-17-6l6-11 14 2-1-5-9-2 2-2q11-12 14-28 0-6 4-11l1-8v-4l10 2 2-3q-5-4-12-4l1-14 14 4 4 1 1-4q-7-4-16-6l4-12v-2l2 1 3 1h11v-3h-2l-9-3 2-7 2-2 1-2h4v-7c-13-1-13-1-25 3m25 6 1 8h1v-6zm0 25-1 4 2-4zm-34 89 1 2Zm-5 17 1 2Z"/>
            <path fill="currentColor" d="M243 49c27 24 47 57 50 94q5 55-27 99l-2 2q-29 38-76 52h-1a126 126 0 0 1-39 4q-16 1-31-3l-2-1q-27-7-48-23l-2-2A142 142 0 0 1 33 79l2-3q18-25 45-41l3-2c51-30 116-21 160 16m-117-8-3 1q-9 2-17 6l-7 2q-13 7-24 16l-4 4-2 1v2l-2 1-5 5v2l-3 1q-6 6-9 13v2l-2 1-13 30a103 103 0 0 0 2 66 113 113 0 0 0 19 37q6 9 14 14l4 5h3l1 3 8 4v2q27 17 58 19h3q23 2 45-7l3-1q22-8 38-25l2-2q36-38 35-89a116 116 0 0 0-13-52h-2l-2-7h-2l-2-6h-2v-2h-2l-1-2-8-11-3-1-2-4c-19-20-47-29-73-30q-17 0-32 2"/>
            <path fill="#A8A8A8" d="M181 71q12 2 19 12 10 14 7 31-3 20-20 33-11 9-14 23v18c0 16-1 31-12 44l-2 2q-10 12-25 13c-10 0-10 0-15-2l4-6v-14l1-1 11 2 1-3h-2q-3 1-5-2l-5-2v-7l1-7q10 0 19 5v-4l-16-6q2-7 6-12l13 2v-3l-9-3 6-8q6-8 8-20l3-9q4-7 4-16l10 1v-3l-10-3v-8l1-6 16 4 2-1v-2l-16-6q1-7 5-14h2l3 1 3 1 7-1-7-3-5-3 2-4 2-3 3-4h3zm0 9 1 8h1v-6zm0 25-1 4 2-4zm-34 89 1 2Zm-5 17 1 2Z"/>
            <path fill="#A8A8A8" d="M181 71q12 2 19 12 10 14 7 31-2 10-7 18h-3l-2-6-7 9-8-3 5-10 5 1v-3h2v-2l-4-2 2-14h5l-3-4-2-1V87l1-5-2-1-1 1h-3v13c-1 12-1 12-3 17l-3 2-3-2-14-5q1-7 5-14h2l3 1 3 1 7-1-7-3-5-3 2-4 2-3 3-4h3zm0 9 1 8h1v-6zm0 25-1 4 2-4z"/>
            <path fill="#A8A8A8" d="m188 80 2 1 1 10-1 3v3l2 1 3 1 1 3h-6l-1 14 3 2v2h-2v3h-5l-5 9 8 3 1-2 5-8 3 2v5l2 1q-6 9-14 15l-7 10q-2 3-5 2h-3l-2-4 2-3-9-2v-2h4l1-3 4 1 6-1 5-6-7-3-1-3h-3l-1-5-10-3v-8l1-6 16 4q5-2 6-6l2-19V81l4 1z"/>
            <path fill="#A8A8A8" d="M125 205q9 0 17 4 2 7 0 13l-7 7-3 5h6l4-4 7 2-3 3h-2v10q-12 5-25 0l4-6v-14l1-1 11 2 1-3h-2q-3 1-5-2l-5-2v-7z"/>
            <path fill="#A8A8A8" d="M202 57q15 5 24 17l6 12 1 2a67 67 0 0 1-17 68h-7v-2h2l1-2 4-6q16-20 13-47-4-24-24-39l-3-2z"/>
            <path fill="#A8A8A8" d="m193 177 4 1 2 5 1 2q6 30-10 56-12 18-33 24l-9 1 7-4q25-11 35-36 8-24 3-49"/>
            <path fill="#A8A8A8" d="M159 131h2l3 1h3l4-1v3l2-1 2 2-1 2h2q4 0 6 3c-6 8-6 8-10 8l-6-1-1 2h-4v2h3l6 2-3 9-2 8-7-2v-11h-6l5-13z"/>
            <path fill="#A8A8A8" d="m148 206 2 1v5l-1 2 2 1-5 9 4 2 7 4 3 1q-5 9-13 12l-3 1-2-6 5-6-5-1-5 5v-2l-5 1-1-2 3-4 2-2 4-3 3-10 1-7z"/>
            <path fill="currentColor" d="M147 171h2v28h-2l-2 7-3-1-4-1-2-1-8-3q2-7 6-12l13 2v-3l-9-3 6-9 1-2zm0 23 1 2Z"/>
            <path fill="#A8A8A8" d="m188 80 2 1 1 10-1 3v3l2 1 3 1 1 3h-6l-1 14 3 2v2h-2v3h-2q-3 0-5 2l-1 1-4-3 1-9 2-2 3-21V81l4 1z"/>
            <path fill="currentColor" d="m167 93 15 3 1 6h-2v4q1 4-2 6l-1 1-2-1-14-5q1-7 5-14"/>
            <path fill="#A8A8A8" d="m193 177 4 1 2 5 1 2q4 20-2 39l-5 1v-7q2-20 0-41"/>
            <path fill="currentColor" d="M125 205q9 0 17 4v7l-1 2-1 2h-2l-2 4q-6-1-7-3l-5-2v-7z"/>
            <path fill="currentColor" d="m160 112 16 4 1 4-7 10-2-1h-2l-2-1-5-2v-8z"/>
            <path fill="currentColor" d="m142 145 10 2-3 13h-2l-1 2-1 3q-5 1-10-1l3-9 1-2 1-3z"/>
            <path fill="currentColor" d="m153 90 3 1 4 1-1 6h-2l-2 7-13-1 1-4v-3l3-6z"/>
            <path fill="currentColor" d="m140 109 14 2v10l-1 3-3 2v-2h-3l-7-1z"/>
            <path fill="currentColor" d="m113 200 2 1 4 1c1 11 1 11-3 15l-10-2v-14z"/>
            <path fill="currentColor" d="m115 184 12 1q-1 8-6 14h-4l-3-1-5-2 2-8h2z"/>
            <path fill="currentColor" d="m130 167 11 4c-8 10-8 10-12 11l-5-1-2-1h-2q2-6 7-10z"/>
            <path fill="currentColor" d="m190 103 9 4q1 7-2 13c-7-2-7-2-9-4z"/>
            <path fill="currentColor" d="m157 195 8 2-1 14-10-3q0-7 3-13"/>
            <path fill="currentColor" d="m163 151 7 2-3 9-2 8-7-2q0-9 5-17"/>
            <path fill="currentColor" d="m151 213 9 3 1 2-7 10-9-3z"/>
            <path fill="currentColor" d="m143 127 5 1h2l2 1v12l-10-2-1-10z"/>
            <path fill="currentColor" d="m181 78 2 14q-4 2-9-1l-2-1-2-1 1-3 3-6q2-5 7-2"/>
            <path fill="currentColor" d="M147 171h2v28q-3-5-2-12l-9-3 6-9 1-2z"/>
            <path fill="currentColor" d="m107 219 10 2 2 14-6-2v-2h-2l-2-4-1-2-2-5z"/>
            <path fill="currentColor" d="M170 77c-4 9-4 9-8 11l-9-2q6-10 17-9"/>
            <path fill="#A8A8A8" d="m142 230 7 2-3 3h-2v10l-7 1-1-5 1-1-2-2z"/>
            <path fill="currentColor" d="M185 122q5 1 9 5l-6 8-8-3z"/>
            <path fill="currentColor" d="m157 175 7 2 1 7v7l-8-3z"/>
            <path fill="currentColor" d="M163 50v1l-2 1h-3l-3 1-6 3-3 1q-6 2-11 7v2l-5 3-4 3q0-5 5-9l4-1 1-3 7-4 2-1c12-6 12-6 18-4"/>
            <path fill="currentColor" d="m110 128 2 1 1 7v7l-3 1v-2H54v-1h57z"/>
            <path fill="currentColor" d="M190 84q6 6 9 13l-1 4-8-4z"/>
            <path fill="currentColor" d="m124 224 9 2q-1 7-7 11h-2l-1-9v-3z"/>
            <path fill="#A8A8A8" d="m179 250 2 1q-12 14-31 15h-2l8-4 8-4 6-1 3-1 1-2z"/>
            <path fill="currentColor" d="m136 170 5 1-3 3-1 2c-4 5-4 5-8 6l-7-1 4-4-1-2 5-1 4 3z"/>
            <path fill="currentColor" d="m160 112 14 2 1 3h-7l-3-3-3 1v7l6 5q-4 2-9-1v-8z"/>
            <path fill="currentColor" d="M175 137q6 1 7 3l-8 8-5-2z"/>
            <path fill="currentColor" d="m159 131 9 1-5 8-2 2-1 1-3-1 1-2 1-5z"/>
            <path fill="currentColor" d="m108 202 2 1 8 1-2 3-2-1v2l-4 6-3-1-1-4v-5z"/>
            <path fill="#A8A8A8" d="M178 115v8l2 1 3 3-1 2h-7l-1 3-1-2-2 1 1-2 4-8 1-4z"/>
            <path fill="#A8A8A8" d="M142 213v8l-7 8-8 9-3-1 3-2 6-9h2l3-6h2v-2q0-3 2-5"/>
            <path fill="#A8A8A8" d="m217 145 1 3h3c-2 6-2 6-5 8h-7v-2h2l1-2z"/>
            <path fill="currentColor" d="m53 146 2 1v16l32 1v1H52z"/>
            <path fill="#A8A8A8" d="M202 57q14 4 23 16h-3l-5-5h-2l-6-4-2-2z"/>
            <path fill="currentColor" d="M205 265v2h-2l-1 2-4 1-2 2-4 1-15 4-6 1v-2l3-1q14-2 27-9z"/>
            <path fill="currentColor" d="M164 16v1h-11l-3 1-16 1h-2v2l-8-1v-2z"/>
            <path fill="currentColor" d="m268 133 1 2h2v3l1 6v18h-2v-4z"/>
            <path fill="currentColor" d="m123 275 10 1h3l18 2v1c-21 2-21 2-31-3z"/>
            <path fill="currentColor" d="m142 230 7 2q-6 5-14 7l3-4 2-2z"/>
            <path fill="#A8A8A8" d="m193 177 4 1q3 4 2 9h-4l-1 3z"/>
            <path fill="currentColor" d="m113 200 2 1 4 1v8l-2 1v-6h-3l-6-2-2 1v-3z"/>
            <path fill="currentColor" d="M167 49h12l15 3-3 1-1 2-2-1-14-4h-7z"/>
            <path fill="currentColor" d="M170 77c-4 9-4 9-8 11h-2l2-1 1-4 1-2v-2h-2l-1 2v-3z"/>
            <path fill="currentColor" d="M150 131h1v8c-6-1-6-1-8-3v-4h5l1 1z"/>
            <path fill="currentColor" d="m95 164-3 4h-2l-3 6-1 2-1 2-4 3 4-8q3-4 3-9z"/>
            <path fill="currentColor" d="m103 188 2 2-1 2-4 15-1 5h-1v-15h2v-3zm-39 52 4 3 2 1 4 5 3 1 4 5-9-4-2-2-2-2v-2l-4-2z"/>
            <path fill="currentColor" d="m146 172 1 4-3 3v4l2 1v2l-8-2 5-8 2-2z"/>
            <path fill="currentColor" d="m177 77 4 1 2 14-4 1 1-3q1-3-2-7z"/>
            <path fill="#A8A8A8" d="m179 247 3 1-9 8-2 2-2 1-3-1q3-4 8-6h2l1-2z"/>
            <path fill="currentColor" d="M51 224q4 1 6 5l1 2 1 4 3 1 2 4c-6-2-10-10-14-15z"/>
            <path fill="currentColor" d="M98 213h2l1 3v4l1 3 3 9h2l2 5q-7-4-9-12l-1-3z"/>
            <path fill="#A8A8A8" d="M153 227q0 5-4 5l-7-2q5-5 11-3"/>
            <path fill="currentColor" d="m134 127 2 1-1 8 2 1v5l-1 3v2l-2 1-1-10v-4z"/>
            <path fill="currentColor" d="m133 104 1 9v14l-4-2v-2l2-13z"/>
            <path fill="currentColor" d="m125 205 3 9v7l-4-2v-7z"/>
            <path fill="currentColor" d="m164 98 2 1-1 3 2 1-3 3 6 1 4 3-11-2q-2-6 1-10"/>
            <path fill="currentColor" d="m175 78 4 1-1 1-1 2-2 2-2 5h4v2q-6-1-7-3l1-2 2-2 1-3z"/>
            <path fill="currentColor" d="m196 41 4 1-1 3 1 3-10-3 1-2 5-1z"/>
            <path fill="currentColor" d="M113 264h5l1 1 8 1 6 2q-10 1-19-2z"/>
            <path fill="currentColor" d="M106 220q5 2 6 6l2 1v2l2 1h-2l-1 3v-2h-2l-2-4-1-2z"/>
            <path fill="currentColor" d="m181 129 3 1h7l-3 5-8-3z"/>
            <path fill="currentColor" d="M140 37h13l8 1v1l-26 1q3-4 5-3"/>
            <path fill="currentColor" d="M58 264q9 3 13 11l-9-5-2-2-2-2z"/>
            <path fill="currentColor" d="M286 197v8l-1 2-1 2q-2-3-2-7h2v-4z"/>
            <path fill="currentColor" d="m142 145 7 1-4 2-2-1v5h-2l-1 4v-7l1-2z"/>
            <path fill="currentColor" d="M48 94h2q-1 7-6 13h-1v-5l2-2z"/>
            <path fill="currentColor" d="M137 266h8v2q-4 2-9 1l-2-2z"/>
            <path fill="currentColor" d="m16 200 4 2 1 6v6l-4-6v-3zm4 0 2 2h-2z"/>
            <path fill="currentColor" d="M113 186h1c-1 7-1 7-3 10h2l-1 2-3-2 2-8h2z"/>
            <path fill="currentColor" d="m130 145 2 2 2 1-1 7-2 1-1-5-1-3v-2z"/>
            <path fill="currentColor" d="m166 136-6 7-3-1 3-1v-5z"/>
            <path fill="currentColor" d="M155 74v3h-3v2l-3 2-4 3 2-6zm12-5h9l1 2-16 1q3-4 6-3"/>
            <path fill="currentColor" d="m138 89 3 1-1 2-5 11q-2-5 1-9l1-3z"/>
            <path fill="currentColor" d="m115 85 2 4h-2l-3 10h-2z"/>
            <path fill="#A8A8A8" d="m202 57 10 4 2 2 2 1q-8-1-14-6z"/>
            <path fill="currentColor" d="M204 50q7 1 13 6l-1 2-6-3-3-2-3-2z"/>
            <path fill="currentColor" d="m166 276 1 2q-4 3-10 2l-1-2z"/>
            <path fill="currentColor" d="M122 192h2l-2 6h-8v-2l3-1 4-1z"/>
            <path fill="currentColor" d="m124 174 3 4q-2 3-5 2h-2q2-6 4-6"/>
            <path fill="#A8A8A8" d="m133 226 1 4-3 4-2 2-2 2-3-1 3-2z"/>
            <path fill="currentColor" d="m98 256 8 3v2l-6-1z"/>
            <path fill="currentColor" d="m136 170 5 1-6 7q-2-5 1-8"/>
            <path fill="currentColor" d="M124 71v8l-6 1 2-4 2-2z"/>
            <path fill="currentColor" d="m226 273 5 2-7 5 1-2-1-4z"/>
            <path fill="#A8A8A8" d="m161 263-9 3h-4q6-5 13-3"/>
            <path fill="currentColor" d="m134 218 2 5h-5l-1-4z"/>
            <path fill="currentColor" d="m80 59 4 1-8 6q0-3 2-5z"/>
            <path fill="#A8A8A8" d="m193 220 5 1v3l-5 1-1-2z"/>
            <path fill="currentColor" d="m228 66 7 2v5l-5-2z"/>
            <path fill="currentColor" d="m117 43-8 4h-2l-2 1q4-6 12-5"/>
            <path fill="currentColor" d="M142 213q2 5-1 8h-2l-1 3-1-2 1-2h2v-2q0-3 2-5"/>
            <path fill="currentColor" d="m140 120 9 3v1l-9-1z"/>
            <path fill="currentColor" d="M140 109h6l-2 1q-4 4-3 9h-1z"/>
            <path fill="currentColor" d="M164 79v5l-4 1v-5z"/>
            <path fill="#A8A8A8" d="m216 149 2 2-4 4-4-1z"/>
            <path fill="#A8A8A8" d="M221 142h1v7l-4-1v-3h2z"/>
            <path fill="currentColor" d="m131 120 3 1v6l-4-2z"/>
            <path fill="#A8A8A8" d="m197 120-2 2h-4v2l-2-1 1-3z"/>
            <path fill="currentColor" d="m213 148 2 1-4 5h-2l-1 2q0-4 3-7z"/>
            <path fill="currentColor" d="M257 102q4 3 5 7l-2 2q-3-4-3-9"/>
            <path fill="currentColor" d="M181 84h1l1 8-4 1z"/>
            <path fill="currentColor" d="M165 81h2l-4 7-3-1h2l1-2z"/>
            <path fill="currentColor" d="m175 78 4 1-4 6h-2z"/>
            <path fill="currentColor" d="m235 73 5 2v3h2v2q-5-1-7-7"/>
            <path fill="currentColor" d="M185 51h8l1 2h-3l-1 2-5-3z"/>
          </svg>
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
            <li><a href="<?= $path ?>admin/" class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-home"></i><span>Dashboard</span></a></li>

            <li><a href="<?= $path . 'admin/collection' ?>"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fa-solid fa-money-bill-1-wave"></i>
                    <span>Collection</span></a></li>
            <li><a href="<?= $path ?>admin/inventory"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-box"></i><span>Inventory</span></a></li>
            <li><a href="<?= $path ?>admin/statistics"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fa-solid fa-chart-line"></i><span>Statistics</span></a></li>
            <li><a href="<?= $path ?>admin/scanner"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-qrcode"></i><span>QR Scanner</span></a></li>

            <li><a href="<?= $path ?>admin/item"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-list"></i><span>Items Management</span></a></li>
            <li><a href="<?= $path ?>admin/users"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-users"></i><span>Users Management</span></a></li>
            <li><a href="<?= $path ?>admin/import"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-import"></i><span>Import Students</span></a></li>
            <li><a href="<?= $path ?>admin/export"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-arrow-up"></i><span>Export Collection</span></a></li>
            <hr>
            <li><a href="<?= $path ?>user/" class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fa-solid fa-file-invoice-dollar"></i><span>My Purchases</span></a></li>
            <li><a href="<?= $path ?>admin/code"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
                        class="fas fa-barcode"></i><span>QR Code</span></a></li>
            <li><a href="<?= $path ?>user/profile"
                    class="flex items-center space-x-2 hover:bg-primary/65 px-2 py-1 rounded"><i
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
