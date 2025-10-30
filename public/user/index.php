<?php
include_once '../includes/partial.php';
include_once '../includes/connect-db.php';
include_once '../includes/token.php';

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
  <link rel="icon" href="./assets/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
      <?php foreach ($purchases as $purchase): ?>
        <div
          class="clickable-div w-full h-auto cursor-pointer <?= $purchase['is_void'] ? 'voided-receipt non-clickable' : '' ?>"
          data-reference="<?= $purchase['reference_no'] ?>" data-ctrl="<?= $purchase['ctrl_no'] ?>"
          data-date="<?= $purchase['date'] ?>" data-quantity="<?= $purchase['quantity'] ?>"
          data-item="<?= $purchase['name'] ?>" data-amount="<?= $purchase['value'] ?>"
          data-inventory="<?= $purchase['idinventory'] ?>" data-mode="<?= $purchase['payment_type'] ?>"
          data-void="<?= $purchase['is_void'] ?>" <?= $purchase['is_void'] ? 'onclick="return false;"' : '' ?>>

          <div class="flex rounded-lg max-w-xs h-full mx-auto bg-white p-4 flex-col items-between 
         shadow-md transition duration-200 transform hover:scale-105 hover:shadow-2xl relative">
            <div class="mb-2 md:mb-4 flex-1">
              <h2 class="text-black font-bold"><?= $purchase['name'] ?></h2>
            </div>

            <div class="text-black text-sm">
              <div class="flex flex-col w-full max-w-md mx-auto">
                <!-- Payment Type -->
                <div class="flex items-center">
                  <div class="px-2 py-1 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                  </div>
                  <div class="flex-1 font-bold px-2 py-1"><?= $purchase['payment_type'] ?></div>
                </div>

                <!-- Value -->
                <div class="flex items-center">
                  <div class="px-2 py-1 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                  </div>
                  <div class="flex-1 font-bold px-2 py-1">₱ <?= number_format($purchase['value'], 2) ?></div>
                </div>
              </div>

              <!-- Date -->
              <div class="flex items-center">
                <div class="px-2 py-1 text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                  </svg>
                </div>
                <div class="flex-1 font-bold px-2 py-1"><?= $purchase['date'] ?></div>
              </div>
            </div>

            <div class="flex flex-col items-center justify-between text-center mt-3">
              <p class="px-4 py-1 bg-gray-200 rounded-full text-gray-700 text-xs font-light">
                Tap to see full details
              </p>
            </div>

          </div>
          <?php if ($purchase['is_void']): ?>
            <div class="z-20 absolute top-0 left-0 h-full w-full flex items-center justify-center 
              bg-gray-600/80 rounded-2xl select-none">
              <p class="leading-relaxed text-4xl font-bold uppercase pointer-events-none"
                style="color: #dc2626; transform: rotate(-45deg);">
                VOIDED
              </p>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

  <!-- Receipt Modal -->
  <div id="myModal" class="modal fixed w-full h-full top-0 left-0 hidden items-center justify-center">
    <!-- hoverlay  -->
    <div id="modalOverlay" class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div
      class="modal-container flex flex-col items-center h-auto bg-white w-11/12 md:max-w-md my-10 mx-auto shadow-lg z-50 overflow-y-auto relative">
      <div class="bg-black w-full grid grid-cols-2 gap-x-4">
        <p class="text-white font-medium ml-3 p-2">MoneyMo</p>
        <button type="button" id="closeModal"
          class="text-white hover:bg-white hover:text-black px-4 py-2 ml-auto cursor-pointer">
          X
        </button>
      </div>
      <!-- Add modal content here -->
      <div class="w-3/4 modal-content py-4 text-left px-6 bg-white">
        <div class="flex justify-center items-center">
          <img src="../assets/acs_logo.jpg" alt="@" class="h-16 m-2">
        </div>
        <div class="flex justify-center items-center">
          <p class="text-1 font-extralight">
            Association of Computer Scientists
          </p>
        </div>
        <div class="flex justify-center items-center pb-2">
          <p class="text-2xl font-bold mt-1">PAYMENT RECEIPT</p>
        </div>
        <div class="py-1 gap-2 w-full border-b-1 border-gray-400">
          <p class="text-xs font-bold text-gray-600">Ref no. <span id="reference"
              class="text-xs font-bold">131231231231</span></p>
          <p class="text-xs font-bold text-gray-600">Ctrl no. <span id="ctrl"
              class="text-xs font-bold">131231231231</span></p>
        </div>
        <div class="border-b border-gray-400 text-black pt-1 pb-1">
          <div class="grid grid-cols-2 gap-x-4">
            <p class="font-semibold text-gray-400">Date</p>
            <p id="date" class="font-semibold">01/01/2025</p>

            <p class="font-semibold text-gray-400">Payment Method</p>
            <p id="mode" class="font-semibold">Gcash</p>

            <p class="font-semibold text-gray-400">Item</p>
            <p id="item" class="font-semibold">Org Fee</p>

            <p class="font-semibold text-gray-400">Quantity</p>
            <p id="quantity" class="font-semibold">ACS</p>

            <p class="font-semibold text-gray-400">Product Price</p>
            <p id="amount" class="font-semibold">₱100.00</p>
          </div>
        </div>

        <div class="border-b border-gray-400 text-black pt-1 pb-1">
          <div class="grid grid-cols-2 gap-x-4">
            <p class="font-bold">Total</p>
            <p id="total" class="font-bold">₱ 100.00</p>
          </div>
        </div>
        <p class="pt-1 pb-1 text-center mt-5">This is a cutomer's copy. Thank You!</p>

        <!-- Void message for modal -->
        <div id="voidMessage" class="hidden">
          <div class="void-text mt-4" style="color: #dc2626;">VOIDED</div>
          <p class="text-center text-gray-600 text-sm mt-2">This receipt has been voided and cannot be downloaded.</p>
        </div>
      </div>
      <div class="my-4 flex justify-center">
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
        $("#quantity").text(quantity);
        $("#item").text(item);
        $("#amount").text("₱ " + amount);
        $("#mode").text(mode);
        $("#total").text("₱ " + amount);

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
      });

      // Show modal when button is clicked (only for non-voided receipts)
      $(document).on("click", ".clickable-div:not(.non-clickable)", function () {
        $("#myModal").removeClass("hidden").addClass("flex");
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