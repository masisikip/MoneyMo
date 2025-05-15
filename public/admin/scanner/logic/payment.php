<?php
include_once '../../../includes/connect-db.php';
include_once '../../../includes/token.php';
require '../../../includes/php/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
    $datetime = date('Y-m-d H:i:s', strtotime('+6 hours'));
    $date = date('F d, Y - h:i A', strtotime('+6 hours'));

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
    $total_items = 0;

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
        $total_items++;
        $ref_val = strval($itemRefCount[$item]);
        $ref_no = str_pad($ref_val, 4, '0', STR_PAD_LEFT);

        $val = strval($cur_num);
        $ctrl_no = str_pad($val, 4, '0', STR_PAD_LEFT);

        $price = $cur_item['value'];
        $reference_no = $cur_item['code'] . "-" . $ref_no;
        $control_no = $prefix['prefix'] . "-" . $ctrl_no;

        $stmt4 = $pdo->prepare('INSERT INTO inventory 
                            (iduser, idofficer, iditem, reference_no, ctrl_no, value, date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt4->execute([$student, $officer, $item, $reference_no, $control_no, $price, $datetime]);

        $stmt5 = $pdo->prepare('UPDATE item SET stock = stock - 1 WHERE iditem = ?');
        $stmt5->execute([$item]);

        $stmt6 = $pdo->prepare('UPDATE item SET sale_count = sale_count + 1 WHERE iditem = ?');
        $stmt6->execute([$item]);

        $cur_num++;
        $amount += $price;
    }

    $pdo->commit();
    // Get student email and name
    $student_email = $user['email'];
    $student_name = $user['f_name'] . ' ' . $user['l_name'];

    // Get officer name
    $stmt9 = $pdo->prepare('SELECT f_name, l_name FROM user WHERE iduser = ?');
    $stmt9->execute([$officer]);
    $officer_data = $stmt9->fetch();
    $officer_name = $officer_data ? $officer_data['f_name'] . ' ' . $officer_data['l_name'] : 'Unknown';

   $stmt10 = $pdo->prepare('
        SELECT i.*, it.name 
        FROM inventory i
        JOIN item it ON i.iditem = it.iditem
        WHERE i.iduser = ? AND i.idofficer = ?
        ORDER BY i.idinventory DESC
        LIMIT ?');
    $stmt10->execute([$student, $officer, count($_POST['iditem'])]);
    $items = $stmt10->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all transaction items again for email
    $items_html = '';
    $total = 0;

    foreach ($items as $item) {
        $stmt = $pdo->prepare('SELECT name FROM item WHERE iditem = ?');
        $stmt->execute([$item['iditem']]);
        $it = $stmt->fetch();

        $reference_no = $item['reference_no'];
        $control_no = $item['ctrl_no'];
        $price = number_format($item['value'], 2);

        $items_html .= "<tr>
            <td>{$it['name']}</td>
            <td>{$reference_no}</td>
            <td>{$control_no}</td>
            <td>₱ {$price}</td>
        </tr>";

        $total += $item['value'];
    }

    $total_html = number_format($total, 2);

    // Create the email body
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Transaction Receipt</title>
    <style>
        @media only screen and (max-width: 600px) {
        .container {
            padding: 15px !important;
        }
        .responsive-table th, .responsive-table td {
            font-size: 13px !important;
            padding: 8px !important;
        }
        .total, .officer {
            text-align: left !important;
        }
        }
    </style>
    </head>
    <body style='font-family: Segoe UI, Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 20px;'>
    <div class='container' style='max-width: 640px; margin: 0 auto; background: #ffffff; padding: 30px 25px; border-radius: 10px; border: 1px solid #e0e0e0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);'>

        <div style='text-align: center; margin-bottom: 20px;'>
        <img src='cid:logoimg' alt='Logo' style='width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; border: 2px solid #ddd;' />
        <h1 style='margin: 0; font-size: 24px; color: #222;'>MoneyMo</h1>
        </div>
        <p style='color: #777; font-size: 14px; margin-bottom: 1px;'>Transaction Date: <strong style='color: #333;'>{$date}</strong></p>
        <p style='color: #777; font-size: 14px; margin-bottom: 1px; margin-top: 2px;'>Recipient's Name: <strong style='color: #333;'>{$student_name}</strong></p>

        <table class='responsive-table' style='width: 100%; border-collapse: collapse; margin-top: 25px;'>
        <thead>
            <tr style='background-color: #f0f0f0;'>
            <th style='text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 14px;'>Item</th>
            <th style='text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 14px;'>Ref No</th>
            <th style='text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 14px;'>Ctrl No</th>
            <th style='text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 14px;'>Price</th>
            </tr>
        </thead>
        <tbody>
            {$items_html}
        </tbody>
        </table>

        <p class='total' style='text-align: right; font-size: 16px; font-weight: bold; margin-top: 25px;'>Total: ₱ {$total_html}</p>
        <p class='officer' style='text-align: right; font-size: 14px; color: #555;'>Transacted by: <strong>{$officer_name}</strong></p>

        <div style='background-color: #f9f9f9; padding: 12px 15px; border-left: 4px solid #00796b; margin-top: 30px; font-size: 14px; color: #444;'>
        This is a customer’s notification email receipt. Please keep this for your records.
        </div>

        <p style='text-align: center; font-size: 13px; color: #888888; margin-top: 35px;'>
        You can view this and past receipts at<br>
        <a href='https://moneymo.miceff.com/' style='color: #007BFF; text-decoration: none;'>https://moneymo.miceff.com/</a>
        </p>

    </div>
    </body>
    </html>

    ";

    // Send Email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';           // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'moneymo@miceff.com';         // Your SMTP username
        $mail->Password = 'man1M0M@sarap';          // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('moneymo@miceff.com', 'MoneyMo');
        $mail->addAddress($student_email, $student_name);

        // Embed logo image (make sure this path is correct or absolute)
        $mail->AddEmbeddedImage('../../../assets/logo-circle.png', 'logoimg', 'logo-circle.png');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your MoneyMo Transaction Receipt';
        $mail->Body = $body;

        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    }
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