<?php
include_once '../includes/partial.php';
include_once '../includes/connect-db.php';
include_once '../includes/token.php';
include_once './card.php';

if (isset($_SESSION['auth_token'])) {
  $payload = decryptToken($_SESSION['auth_token']);
  if ($payload && isset($payload['user_type'])) {
    $iduser = $payload['user_id'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MoneyMo - Purchases</title>
  <link rel="stylesheet" href="../css/styles.css" />
  <?php include_once '../includes/favicon.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .voided-receipt {
      filter: grayscale(100%);
      opacity: 0.7;
      pointer-events: none;
    }

    .void-text {
      color: #dc2626 !important;
      font-weight: bold;
      font-size: 16px;
      text-align: center;
      margin-top: 8px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .non-clickable {
      pointer-events: none;
      cursor: default;
    }
  </style>
</head>

<body class="bg-base-300 flex flex-col w-screen min-h-screen overflow-x-hidden">
  <?php include_once '../includes/partial.php'; ?>

  <!--Receipts-->
  <div class="flex flex-col w-full h-full min-h-screen items-center pt-4 md:p-4">
    <?php
    $stmt = $pdo->prepare("
        SELECT
        reference_no,
        ctrl_no,
          date(date) AS date,
          quantity,
          name,
          inventory.value,
          idinventory,
          ctrl_no,
          is_void,
          CASE
          WHEN payment_type = 0 THEN 'Cash'
              WHEN payment_type = 1 THEN 'Gcash'
              ELSE 'unknown'
        END AS payment_type
      FROM inventory
      INNER JOIN item on inventory.iditem = item.iditem
      WHERE iduser = ?
      ORDER BY date DESC, ctrl_no DESC");

    $stmt->execute([$iduser]);
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Receipts Grid -->
    <div class="w-full p-2 md:p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 md:w-10/12 gap-2 md:gap-4">
      <?php foreach ($purchases as $purchase){
        renderPurchaseCard($purchase);
      } ?>
    </div>
  </div>

  <script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

  <?php
  include_once './receipt_modal.php';
  include_once '../includes/footer.php'; ?>

  <script>
    let qrFileName = "";
    let currentIsVoid = false;

    document
      .querySelector("#downloadButton")
      .addEventListener("click", function () {
        if (currentIsVoid) {
          alert("This receipt has been voided and cannot be downloaded.");
          return false;
        }

        html2canvas(document.querySelector(".modal-content"), {
          onrendered: function (canvas) {
            const imgDataUrl = canvas.toDataURL("image/png");
            const link = document.createElement("a");
            link.href = imgDataUrl;
            link.download = qrFileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          },
        });
      });

    function downloadReceipt(element) {
      html2canvas(document.querySelector(".modal-content"), {
        onrendered: function (canvas) {
          const imgDataUrl = canvas.toDataURL("image/png");
          const link = document.createElement("a");
          link.href = imgDataUrl;
          link.download = qrFileName;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        },
      });
    }

    $(document).ready(function () {
      $(document).on("click", ".clickable-div:not(.non-clickable)", function () {
        let reference = $(this).data("reference");
        let ctrl = $(this).data("ctrl");
        let date = $(this).data("date");
        let quantity = $(this).data("quantity");
        let item = $(this).data("item");
        let amount = $(this).data("amount");
        let mode = $(this).data("mode");
        let isVoid = $(this).data("void");

        currentIsVoid = isVoid;
        qrFileName = "QR_" + reference + ".png"

        $("#reference").text(reference);
        $("#ctrl").text(ctrl);
        $("#date").text(date);
        $("#quantity").text("x " + quantity);
        $("#item").text(item);
        $("#amount").text("₱ " + Number(amount).toFixed(2));
        $("#mode").text(mode);
        $("#total").text("₱ " + Number(amount).toFixed(2));

        // Update modal for void status
        if (isVoid) {
          $('.modal-content').addClass('opacity-70');
          $('#voidMessage').removeClass('hidden');
          $('#downloadButton').addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        } else {
          $('.modal-content').removeClass('opacity-70');
          $('#voidMessage').addClass('hidden');
          $('#downloadButton').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        }

        $("#myModal")[0].showModal();
      });

      document.getElementById("myModal").addEventListener("close", function () {
        $('.modal-content').removeClass('opacity-70');
        $('#voidMessage').addClass('hidden');
        $('#downloadButton').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
      });
    });

    $(document).ready(function () {
      $('#header-title').text('My Purchases');
    })
  </script>
</body>

</html>
