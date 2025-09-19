<?php
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
</head>

<body class="bg-[#d9d9d9] flex flex-col w-screen min-h-screen overflow-x-hidden">
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

  <!-- Testing -->
  <div class="container px-12 w-full h-full min-h-screen items-center">
    <div class="container mx-auto pt-10 md:px-10">
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
          CASE 
          WHEN payment_type = 0 THEN 'Cash'
              WHEN payment_type = 1 THEN 'Gcash'
              ELSE 'unknown'
        END AS 	payment_type
      FROM inventory
      INNER JOIN item on inventory.iditem = item.iditem
      WHERE iduser = ?
      ORDER BY date DESC, ctrl_no DESC");

      $stmt->execute([$iduser]);
      $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

      ?>

      <!-- Receipts Grid -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-x-5 gap-y-10 mx-0 md:mx-2 px-0 md:px-5">
        <!-- Receipt 1 -->
        <?php foreach ($purchases as $purchase): ?>
          <div class="clickable-div relative p-3 bg-white md:w-[16rem] w-11/12 h-auto md:h-72 rounded-lg shadow-md hover:shadow-xl place-items-center cursor-pointer transform transition-transform duration-300 hover:scale-110 hover:z-10" data-reference="<?= $purchase['reference_no'] ?>"
            data-reference="<?= $purchase['ctrl_no'] ?>"
            data-date="<?= $purchase['date'] ?>" data-quantity="<?= $purchase['quantity'] ?>"
            data-item="<?= $purchase['name'] ?>" data-amount="<?= $purchase['value'] ?>"
            data-inventory="<?= $purchase['idinventory'] ?>" data-mode="<?= $purchase['payment_type'] ?>">
            <div class="flex items-center justify-center mb-2 pb-2">
              <h2 class="text-black text-lg font-bold mr-1">PAYMENT RECEIPT</h2>
            </div>

            <div class="pb-3 border-b border-black text-black border-t pt-2">
              <div class="grid grid-cols-2 gap-x-4">
                <p>Date:</p>
                <p class="text-right font-bold"><?= $purchase['date'] ?></p>

                <p>Item:</p>
                <p class="text-right font-bold"><?= $purchase['name'] ?></p>

                <p>Mode:</p>
                <p class="text-right font-bold"><?= $purchase['payment_type'] ?></p>

                <p>Paid:</p>
                <p class="text-right font-bold">₱ <?= $purchase['value'] ?></p>
              </div>
            </div>
            <div class="flex flex-col justify-between flex-grow text-center">
              <p class="leading-relaxed text-base text-gray-800 my-3 font-light">
                Tap to see full details
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>



  <script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

  <!-- Receipt Modal -->
  <div id="myModal" class="modal fixed w-full h-full top-0 left-0 hidden items-center justify-center z-20">
    <!-- hoverlay  -->
    <div id="modalOverlay" class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div
      class="modal-container flex flex-col items-center h-auto bg-white w-11/12 md:max-w-md my-10 mx-auto shadow-lg z-50 overflow-y-auto">
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
          <p class="text-xs font-bold text-gray-600">Ref no. <span id="reference" class="text-xs font-bold">131231231231</span></p>
          <p class="text-xs font-bold text-gray-600">Ctrl no. <span id="ctrl" class="text-xs font-bold">131231231231</span></p>

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

    document
      .querySelector("#downloadButton")
      .addEventListener("click", function() {
        html2canvas(document.querySelector(".modal-content"), {
          onrendered: function(canvas) {
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
        onrendered: function(canvas) {
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

    $(document).ready(function() {
      $(document).on("click", ".clickable-div", function() {
        let reference = $(this).data("reference");
        let ctrl = $(this).data("ctrl");
        let date = $(this).data("date");
        let quantity = $(this).data("quantity");
        let item = $(this).data("item");
        let amount = $(this).data("amount");
        let mode = $(this).data("mode");
        let ctrl_no = $(this).data("ctrl_no");

        qrFileName = "QR_" + reference + ".png"

        $("#reference").text(reference);
        $("#ctrl").text(ctrl);
        $("#date").text(date);
        $("#quantity").text(quantity);
        $("#item").text(item);
        $("#amount").text("₱ " + amount);
        $("#mode").text(mode);
        $("#total").text("₱ " + amount);

      });

      // Show modal when button is clicked
      $(".clickable-div").click(function() {
        $("#myModal").removeClass("hidden").addClass("flex");
      });

      // Hide modal when close button is clicked
      $("#closeModal").click(function() {
        $("#myModal").addClass("hidden").removeClass("flex");
      });
    });

    $(document).ready(function() {
      $('#header-title').text('My Purchases');
    })
  </script>
</body>

</html>