<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MoneyMo</title>
  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="icon" href="./assets/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-[#d9d9d9]">

  <?php include_once '../includes/partial.php' ?>


  <!--Receipts-->
  <div class="flex flex-wrap justify-center mt-3">
    <!-- card 1 -->
    <div class="clickable-div p-4 max-w-sm cursor-pointer">
      <div class="flex rounded-lg h-full bg-white p-8 flex-col hover:shadow-2xl">
        <div class="flex items-center mb-2 pb-3 border-b-1 border-black">
          <h2 class="text-black text-lg font-bold mr-1">PAYMENT RECEIPT</h2>
          <div class="w-8 h-8 mr-3 inline-flex items-center justify-center flex-shrink-0 ml-auto bg-amber-200">
            <svg class="w-6 h-6 text-black mt-3 text-blackinline-flex items-center" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3" />
            </svg>
          </div>
        </div>

        <div class="pb-3 border-b border-black text-black">
          <div class="grid grid-cols-2 gap-x-4">
            <p>Item:</p>
            <p class="text-right font-bold">Org fee</p>

            <p>Mode:</p>
            <p class="text-right font-bold">Gcash</p>

            <p>Paid:</p>
            <p class="text-right font-bold">P 100.00</p>
          </div>
        </div>

        <div class="flex flex-col justify-between flex-grow text-center">
          <p class="leading-relaxed text-base text-gray-800 my-3 font-light">
            Tap to see full details
          </p>
        </div>
      </div>
    </div>

    <!-- card 2-->
    <div class="clickable-div p-4 max-w-sm cursor-pointer">
      <div class="flex rounded-lg h-full bg-white p-8 flex-col hover:shadow-2xl">
        <div class="flex items-center mb-2 pb-3 border-b-1 border-black">
          <h2 class="text-black text-lg font-bold mr-1">PAYMENT RECEIPT</h2>
          <div class="w-8 h-8 mr-3 inline-flex items-center justify-center text-white flex-shrink-0 ml-auto">
            <svg class="w-6 h-6 text-black mt-3 text-blackinline-flex items-center" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3" />
            </svg>
          </div>
        </div>

        <div class="pb-3 border-b border-black text-black">
          <div class="grid grid-cols-2 gap-x-4">
            <p>Item:</p>
            <p class="text-right font-bold">Org Shirt</p>

            <p>Mode:</p>
            <p class="text-right font-bold">Gcash</p>

            <p>Paid:</p>
            <p class="text-right font-bold">P 500.00</p>
          </div>
        </div>

        <div class="flex flex-col justify-between flex-grow text-center">
          <p class="leading-relaxed text-base text-gray-800 my-3 font-light">
            Tap to see full details
          </p>
        </div>
      </div>
    </div>

    <!-- card 3-->
    <div class="clickable-div p-4 max-w-sm cursor-pointer">
      <div class="flex rounded-lg h-full bg-white p-8 flex-col hover:shadow-2xl">
        <div class="flex items-center mb-2 pb-3 border-b-1 border-black">
          <h2 class="text-black text-lg font-bold mr-1">PAYMENT RECEIPT</h2>
          <div class="w-8 h-8 mr-3 inline-flex items-center justify-center text-white flex-shrink-0 ml-auto">
            <svg class="w-6 h-6 text-black mt-3 text-blackinline-flex items-center" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3" />
            </svg>
          </div>
        </div>

        <div class="pb-3 border-b border-black text-black">
          <div class="grid grid-cols-2 gap-x-4">
            <p>Item:</p>
            <p class="text-right font-bold">Org ID Lace</p>

            <p>Mode:</p>
            <p class="text-right font-bold">Gcash</p>

            <p>Paid:</p>
            <p class="text-right font-bold">P 200.00</p>
          </div>
        </div>

        <div class="flex flex-col justify-between flex-grow text-center">
          <p class="leading-relaxed text-base text-gray-800 my-3 font-light">
            Tap to see full details
          </p>
        </div>
      </div>
    </div>

    <!-- card 4-->
    <div class="clickable-div p-4 max-w-sm cursor-pointer">
      <div class="flex rounded-lg h-full bg-white p-8 flex-col hover:shadow-2xl">
        <div class="flex items-center mb-2 pb-3 border-b-1 border-black">
          <h2 class="text-black text-lg font-bold mr-1">PAYMENT RECEIPT</h2>
          <div class="w-8 h-8 mr-3 inline-flex items-center justify-center text-white flex-shrink-0 ml-auto">
            <svg class="w-6 h-6 text-black mt-3 text-blackinline-flex items-center" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3" />
            </svg>
          </div>
        </div>

        <div class="pb-3 border-b border-black text-black">
          <div class="grid grid-cols-2 gap-x-4">
            <p>Item:</p>
            <p class="text-right font-bold">Night Gala</p>

            <p>Mode:</p>
            <p class="text-right font-bold">Gcash</p>

            <p>Paid:</p>
            <p class="text-right font-bold">P 550.00</p>
          </div>
        </div>

        <div class="flex flex-col justify-between flex-grow text-center">
          <p class="leading-relaxed text-base text-gray-800 my-3 font-light">
            Tap to see full details
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div id="myModal" class="modal fixed w-full h-full top-0 left-0 hidden items-center justify-center">
    <!-- hoverlay  -->
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div class="modal-container h-auto bg-white w-11/12 md:max-w-md mx-auto shadow-lg z-50 overflow-y-auto">
      <div class="bg-black grid grid-cols-2 gap-x-4">
        <p class="text-white font-medium ml-3 p-2">MoneyMo</p>
        <button type="button" id="closeModal" class="text-white hover:bg-white hover:text-black px-4 py-2 ml-auto">
          X
        </button>
      </div>
      <!-- Add modal content here -->
      <div class="modal-content py-4 text-left px-6">
        <div class="flex justify-center items-center">
          <p class="text-3xl font-bold">@</p>
        </div>
        <div class="flex justify-center items-center">
          <p class="text-1 font-extralight">
            Association of Computer Scientists
          </p>
        </div>
        <div class="flex justify-center items-center pb-2 border-b-1 border-gray-400">
          <p class="text-2xl font-bold mt-1">PAYMENT RECEIPT</p>
        </div>
        <div class="border-b border-gray-400 text-black pt-1 pb-1">
          <div class="grid grid-cols-2 gap-x-4">
            <p class="font-semibold text-gray-400">Merchant</p>
            <p class="font-semibold">ACS</p>

            <p class="font-semibold text-gray-400">Date</p>
            <p class="font-semibold">01/01/2025</p>

            <p class="font-semibold text-gray-400">Payment Method</p>
            <p class="font-semibold">Gcash</p>

            <p class="font-semibold text-gray-400">Item</p>
            <p class="font-semibold">Org Fee</p>
          </div>
        </div>

        <div class="border-b border-gray-400 text-black pt-1 pb-1">
          <div class="grid grid-cols-2 gap-x-4">
            <p class="font-semibold text-gray-400">Product Price</p>
            <p class="font-semibold">100.00</p>

            <p class="font-semibold text-gray-400">Discount</p>
            <p class="font-semibold">0.00</p>
          </div>
        </div>

        <div class="border-b border-gray-400 text-black pt-1 pb-1">
          <div class="grid grid-cols-2 gap-x-4">
            <p class="font-bold">Total</p>
            <p class="font-bold">P 100.00</p>
          </div>
        </div>
        <p class="pt-1 pb-1">This is a cutomer's copy. Thank You!</p>
        <div class="mt-4 flex justify-center">
          <button
            class="px-20 bg-black p-3 ml-3 rounded-lg text-white hover:bg-white hover:border-1 hover:text-black hover:shadow-2xl hover:font-bold">
            Download
          </button>
        </div>
      </div>
    </div>
  </div>


  <script>
    $(document).ready(function () {
      $(".clickable-div").click(function () {
        $("#myModal").addClass("flex").removeClass("hidden");
      });
    });

    $(document).ready(function () {
      $("#closeModal").click(function () {
        $("#myModal").addClass("hidden").removeClass("flex");
      });
    });
  </script>
</body>

</html>