<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MoneyMo</title>
  <link rel="stylesheet" href="../../css/styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="icon" href="./assets/favicon.ico" type="image/x-icon" />
  <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-[#d9d9d9]">

  <?php include_once '../../includes/partial.php' ?>

  <div class="modal  w-full h-full top-0 left-0 flex items-center justify-center">
    <div class="modal-container h-auto bg-white w-11/12 md:max-w-md mx-auto shadow-lg z-50 overflow-y-auto rounded-lg">
      <!-- Add modal content here -->
      <div class="modal-content py-4 text-left px-15">
        <div class="grid grid-cols-2 justify-center items-center pb-2 border-b-2 border-gray-400 px-5">
          <div class="flex justify-center">
            <img class="rounded-full w-20 h-20 border-1" src="../../assets/default.jpg" alt="image description" />
          </div>
          <div class="">
            <p class="font-bold">TIMOSA, JOVAN P.</p>
            <p>2022-8-0218</p>
            <p class="text-sm">Year 3 Block 1</p>
          </div>
        </div>
        <div class="border-gray-400 text-black pt-1 pb-1">
          <p class="flex justify-center font-bold mb-1">My QR Code</p>
          <div class="flex justify-center">
            <img class="w-50 h-50" src="../../assets/default_qr.jpg" alt="image description" />
          </div>
        </div>

        <div class="mt-4 flex justify-center">
          <button
            class="px-20 bg-black p-3 ml-3 rounded-lg text-white hover:bg-white hover:border-1 hover:text-black hover:shadow-2xl hover:font-bold">
            Download
          </button>
        </div>
      </div>
    </div>
  </div>
  <script></script>
</body>

</html>