<dialog id="myModal" class="modal">
  <div class="modal-box w-11/12 md:max-w-sm p-0 pt-5">
    <form method="dialog" class="absolute left-0 top-0 bg-base-300 flex items-center p-2 w-full">
      <div class="flex-1 flex gap-1">
        <img
          src="../assets/logo-circle.png"
          alt="MoneyMo Logo" class="h-6 w-6 object-contain" />
        <p class="text-white font-semibold">MoneyMo</p>
      </div>
      <button class="btn btn-sm btn-circle btn-ghost">✕</button>
    </form>
    <div class="w-full modal-content py-6 px-12 bg-white text-gray-800 text-sm font-sans">
      <!-- Header -->
      <div class="flex flex-col items-center py-3">
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

    <div class="my-4 flex justify-center">
      <button id="downloadButton" class="btn btn-primary">Download</button>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button>close</button>
  </form>
</dialog>
