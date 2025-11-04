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

<body class="bg-[#d9d9d9] flex flex-col w-screen min-h-screen overflow-x-hidden">
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

  <!-- Receipt Modal -->
  <div id="myModal" class="modal fixed w-full h-full top-0 left-0 hidden items-center justify-center">
    <!-- hoverlay  -->
    <div id="modalOverlay" class="modal-overlay absolute top-0 left-0 w-full h-full bg-gray-900 opacity-50"></div>

    <div
      class="modal-container flex flex-col items-center h-[90%] bg-white w-11/12 md:max-w-sm my-10 mx-auto rounded-md shadow-lg z-50 overflow-y-auto relative">
      <div class="bg-black w-full grid grid-cols-2 gap-x-4">
        <div class="flex items-center gap-1 ml-2">
          <img
            src="<?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ?>/MoneyMo/public/assets/logo-circle.png"
            alt="MoneyMo Logo" class="h-6 w-6 object-contain" />
          <p class="text-white font-semibold">MoneyMo</p>
        </div>
        <button type="button" id="closeModal"
          class="text-white hover:bg-white hover:text-black px-3 py-1 m-1.5 ml-auto cursor-pointer rounded-full">
          X
        </button>
      </div>
      <!-- Receipt Modal Content -->
      <div class="w-full modal-content py-6 px-12 bg-white text-gray-800 text-sm font-sans">

        <!-- Header -->
        <div class="flex flex-col items-center pb-3">
          <img src="../assets/acs_logo.jpg" alt="ACS Logo" class="h-16 mb-2 object-contain">
          <p class="text-gray-500 text-xs tracking-wide font-light uppercase">Association of Computer Scientists</p>
          <p class="text-2xl font-bold mt-1 text-gray-800">PAYMENT RECEIPT</p>
        </div>

        <!-- Reference Section -->
        <div class="py-2 border-b border-gray-300 mt-2">
          <div class="flex justify-between text-xs text-gray-600 font-semibold">
            <p>Ref No: <span id="reference" class="text-gray-800 font-bold">131231231231</span></p>
            <p>Ctrl No: <span id="ctrl" class="text-gray-800 font-bold">131231231231</span></p>
          </div>
        </div>

        <!-- Details Section -->
        <div class="py-4 border-b border-gray-300">
          <div class="grid grid-cols-2 gap-y-2 gap-x-4">
            <p class="font-semibold text-gray-400">Date:</p>
            <p id="date" class="font-semibold text-gray-800 text-right">01/01/2025</p>

            <p class="font-semibold text-gray-400">Payment Method:</p>
            <p id="mode" class="font-semibold text-gray-800 text-right">Gcash</p>

            <p class="font-semibold text-gray-400">Item:</p>
            <p id="item" class="font-semibold text-gray-800 text-right">Org Fee</p>
          </div>
        </div>

        <!-- Quantity & Product Price Section -->
        <div class="py-4 border-b border-gray-300">
          <div class="grid grid-cols-2 gap-y-2 gap-x-4">
            <p class="font-semibold text-gray-400">Product Price:</p>
            <p id="amount" class="font-semibold text-gray-800 text-right">₱100.00</p>

            <p class="font-semibold text-gray-400">Quantity:</p>
            <p id="quantity" class="font-semibold text-gray-800 text-right">x 1</p>
          </div>
        </div>

        <!-- Total Section -->
        <div class="py-4 border-b border-gray-300">
          <div class="flex justify-between items-center">
            <p class="font-bold text-gray-800 uppercase tracking-wide">Total</p>
            <p id="total" class="font-bold text-gray-900 text-lg">₱100.00</p>
          </div>
        </div>

        <!-- Footer -->
        <p class="pt-3 text-center mt-6 text-xs text-gray-600 border-t border-dashed border-gray-400">
          --- Customer Copy ---<br>
          Thank you for your purchase!
        </p>

        <!-- Voided Message -->
        <div id="voidMessage" class="hidden mt-4 text-center">
          <div class="text-3xl font-bold uppercase text-red-600 tracking-widest opacity-80 rotate-[-10deg] select-none">
            VOIDED
          </div>
          <p class="text-gray-600 text-xs mt-2">This receipt has been voided and cannot be downloaded.</p>
        </div>
      </div>

      <div class="mb-4 flex justify-center">
        <button id="downloadButton"
          class="px-20 bg-black p-3 ml-3 rounded-lg text-white hover:bg-white border-1 hover:border-1 hover:text-black hover:shadow-2xl cursor-pointer">
          Download
        </button>
      </div>
    </div>
  </div>

  <?php include_once '../includes/footer.php'; ?>

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

        // Finally show the modal
        $("#myModal").removeClass("hidden").addClass("flex");
        console.log("✅ clickable-div clicked:", this);
      });

      // Hide modal when close button is clicked
      $("#closeModal").click(function () {
        $("#myModal").addClass("hidden").removeClass("flex");
        // Reset modal state
        $('.modal-content').removeClass('opacity-70');
        $('#voidMessage').addClass('hidden');
        $('#downloadButton').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
      });

      // Also close modal when clicking overlay
      $("#modalOverlay").click(function () {
        $("#myModal").addClass("hidden").removeClass("flex");
        // Reset modal state
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
