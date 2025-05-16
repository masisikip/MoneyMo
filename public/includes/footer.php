<footer
    class="<?= $usertype == 1 ? 'bg-white text-black' : 'bg-black text-white' ?> border-t border-gray-300 p-6 mt-10">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
        <div class="md:col-span-1 w-3/4">
            <h2 class="font-bold text-lg">MoneyMo</h2>
            <p class="mt-2 text-justify">
                Money Monitor: An automated cash collection and inventory tracking system.

            </p>
            <p class="mt-2 text-justify">
                Developed by computer science students at Palawan State University.
            </p>
        </div>

        <div class="md:col-span-2">
            <h2 class="font-bold text-lg">Contributors</h2>
            <?php
            // Define your contributors as an array of [url, name]
            $contributors = [
                ['https://github.com/23jammy', 'Jamaica Magbanua'],
                ['https://github.com/Angela1104', 'Karen Angela Realubit'],
                ['https://github.com/Carl2121', 'Carlos Heredero'],
                ['https://github.com/crishelpc', 'Crishel Ponce'],
                ['https://github.com/FPPinedaJr', 'Fernando Pineda Jr.'],
                ['https://github.com/JacobRyan102397', 'Jacob Ryan Rabang'],
                ['https://github.com/jhnvincent', 'John Vincent Labotoy'],
                ['https://github.com/JovTim', 'Jovan Timosa'],
                ['https://github.com/marc-sol', 'Marc Solidum'],
                ['https://github.com/uzzielkyle', 'Uzziel Kyle Ynciong'],
                ['https://github.com/JianZcar', 'Jian Z\'car Esteban'],
                ['https://github.com/vincentvigonte', 'Vincent Vigonte'],
                ['https://github.com/mrkalilano', 'Mark Joseph Alilano'],
                ['https://github.com/maico', 'Laurence Michael Maico'],
                ['https://github.com/tianala', 'Christian Ala'],
            ];

            // Shuffle the array to randomize order
            shuffle($contributors);
            ?>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mt-2 text-blue-400">
                <?php foreach ($contributors as [$url, $name]): ?>
                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="hover:underline hover:text-blue-600">
                        <?= htmlspecialchars($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <div class="text-center text-xs mt-8 text-gray-500">
        &copy; <?= date('Y') ?> <a href="https://moneymo.miceff.com" class="hover:underline">MoneyMo</a>. All rights
        reserved.
        <br>
        View on <a href="https://github.com/masisikip/MoneyMo" target="_blank"
            class="hover:underline text-blue-400 hover:text-blue-600">GitHub</a>
    </div>
</footer>