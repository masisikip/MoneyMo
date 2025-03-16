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
    $student_id = $_POST['student_id'] ?? '';

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

    $pdo->beginTransaction();

    foreach ($_POST['iditem'] as $item) {
        $stmt3 = $pdo->prepare('SELECT * FROM item WHERE iditem = ?');
        $stmt3->execute([$item]);
        $cur_item = $stmt3->fetch();
        
        $val = strval($cur_num);
        $receipt_no = str_pad($val, 4, '0', STR_PAD_LEFT);

        if (!$cur_item) {
            continue; 
        }

        $reference_no = "ACS-" . $cur_item['code'] . "-" . date("Ymd") . "-" . $receipt_no;

        $stmt4 = $pdo->prepare('INSERT INTO inventory 
                                (iduser, idofficer, iditem, reference_no) 
                                VALUES (?, ?, ?, ?)');
        $stmt4->execute([$student, $officer, $item, $reference_no]);

        $stmt5 = $pdo->prepare('UPDATE item SET stock = stock - 1 WHERE iditem = ?');
        $stmt5->execute([$item]);

        $cur_num ++;
        $amount += $cur_item['value'];
    }

    $pdo->commit(); 
}
?>

<div id="receipt" class="w-full h-full fixed top-0 left-0 flex justify-center items-center bg-gray-700/50">
    <div id="receipt-main" class="w-[24rem] py-8 flex flex-col items-center bg-white rounded-lg shadow">
        <div class="border-b border-gray-300 flex flex-col items-center justify-center">
            <i class="fa-solid fa-circle-check text-black text-[6rem] mb-2"></i>
            <p class="font-semibold text-2xl">Thank You</p>
            <span class="text-gray-500 text-wrap w-2/3 my-2 text-center text-sm">Your payment has been successfully processed</span>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <span class="text-gray-500 text-sm font-bold">ACCOUNT NAME</span>
            <span class="text-xl font-bold"><?= htmlspecialchars($user['f_name']) ?> <?= htmlspecialchars($user['l_name']) ?></span>
        </div>
        <div class="mt-4 flex flex-col items-center">
            <span class="text-gray-500 text-sm font-bold">TOTAL AMOUNT</span>
            <span class="text-xl font-bold">₱ <?= number_format($amount, 2) ?></span>
        </div>
        <button class="w-30 p-1 text-white bg-black rounded mt-6 hover:bg-gray-800 cursor-pointer close-btn">Close</button>
    </div>
</div>

<script>
    $(document).on('click', '.close-btn', function() {
        $('#receipt').remove();
    });
</script>
