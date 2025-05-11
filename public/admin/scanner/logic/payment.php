<?php
include_once '../../../includes/connect-db.php';
include_once '../../../includes/token.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['auth_token'])) {
    $payload = decryptToken($_SESSION['auth_token']);
    if ($payload && isset($payload['user_type'])) {
        $officer = $payload['user_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = '2022-8-0193';
    // $student_id = $_POST['student_id'] ?? ''; 

    $stmt1 = $pdo->prepare('SELECT * FROM user WHERE student_id = ?');
    $stmt1->execute([$student_id]);
    $user = $stmt1->fetch();

    if (!$user) {
        die('Invalid student ID.');
    }

    $stmt2 = $pdo->prepare('SELECT COUNT(*) AS inv_count FROM inventory');
    $stmt2->execute();
    $num = $stmt2->fetch();
    $control_num = $num['inv_count'];
    $cur_num = $control_num + 1;

    $student = $user['iduser'];

    $amount = 0;

    if (!isset($_POST['iditem']) || empty($_POST['iditem'])) {
        die('No items selected.');
    }

    $stmt8 = $pdo->prepare('SELECT prefix FROM ctrl_no ORDER BY idctrl_no DESC LIMIT 1');
    $stmt8->execute();
    $prefix = $stmt8->fetch();

    $pdo->beginTransaction();

    $itemRefCount = [];

    foreach ($_POST['iditem'] as $item) {
        $stmt3 = $pdo->prepare('SELECT * FROM item WHERE iditem = ?');
        $stmt3->execute([$item]);
        $cur_item = $stmt3->fetch();

        if (!$cur_item) {
            continue;
        }

        // Check existing count only once per item
        if (!isset($itemRefCount[$item])) {
            $stmt7 = $pdo->prepare('SELECT COUNT(*) AS item_count FROM inventory WHERE iditem = ?');
            $stmt7->execute([$item]);
            $cur_ref = $stmt7->fetch();
            $itemRefCount[$item] = $cur_ref['item_count'];
        }

        $itemRefCount[$item]++; // increment for current transaction
        $ref_val = strval($itemRefCount[$item]);
        $ref_no = str_pad($ref_val, 4, '0', STR_PAD_LEFT);

        $val = strval($cur_num);
        $ctrl_no = str_pad($val, 4, '0', STR_PAD_LEFT);

        $price = $cur_item['value'];
        $reference_no = $prefix['prefix'] . "-" . $cur_item['code'] . "-" . $ref_no;
        $control_no = $prefix['prefix'] . "-" . $ctrl_no;

        $stmt4 = $pdo->prepare('INSERT INTO inventory 
                            (iduser, idofficer, iditem, reference_no, ctrl_no, value) 
                            VALUES (?, ?, ?, ?, ?, ?)');
        $stmt4->execute([$student, $officer, $item, $reference_no, $control_no, $price]);

        $stmt5 = $pdo->prepare('UPDATE item SET stock = stock - 1 WHERE iditem = ?');
        $stmt5->execute([$item]);

        $stmt6 = $pdo->prepare('UPDATE item SET sale_count = sale_count + 1 WHERE iditem = ?');
        $stmt6->execute([$item]);

        $cur_num++;
        $amount += $price;
    }

    $pdo->commit();
}
?>

<div id="receipt" class="w-full h-full fixed top-0 left-0 flex justify-center items-center bg-gray-700/50">
    <div id="receipt-main" class="w-[24rem] py-8 flex flex-col items-center bg-white rounded-lg shadow">
        <div class="border-b border-gray-300 flex flex-col items-center justify-center">
            <i class="fa-solid fa-circle-check text-black text-[6rem] mb-2"></i>
            <p class="font-semibold text-2xl">Thank You</p>
            <span class="text-gray-500 text-wrap w-2/3 my-2 text-center text-sm">Your payment has been successfully
                processed</span>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <span class="text-gray-500 text-sm font-bold">ACCOUNT NAME</span>
            <span class="text-xl font-bold"><?= htmlspecialchars($user['f_name']) ?>
                <?= htmlspecialchars($user['l_name']) ?></span>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <span class="text-gray-500 text-sm font-bold">TOTAL AMOUNT</span>
            <span class="text-xl font-bold">₱ <?= number_format($amount, 2) ?></span>
        </div>
        <button
            class="w-30 p-1 text-white bg-black rounded mt-6 hover:bg-gray-800 cursor-pointer close-btn">Close</button>
    </div>
</div>

<script>
    $(document).on('click', '.close-btn', function () {
        $('#receipt').remove();
    });
</script>