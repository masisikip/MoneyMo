<?php
function renderPurchaseCard(array $purchase): void
{
  $isVoid = $purchase['is_void'];
  $classes = "max-w-xs w-auto h-auto cursor-pointer clickable-div hover:scale-110 hover:z-99 transition-all";
  if ($isVoid) {
    $classes .= " voided-receipt non-clickable";
  }

  $dataAttrs = sprintf(
    'data-reference="%s" data-ctrl="%s" data-date="%s" data-quantity="%s" data-item="%s" data-amount="%s" data-inventory="%s" data-mode="%s" data-void="%s"',
    htmlspecialchars($purchase['reference_no']),
    htmlspecialchars($purchase['ctrl_no']),
    htmlspecialchars($purchase['date']),
    htmlspecialchars($purchase['quantity']),
    htmlspecialchars($purchase['name']),
    htmlspecialchars($purchase['value']),
    htmlspecialchars($purchase['idinventory']),
    htmlspecialchars($purchase['payment_type']),
    htmlspecialchars($purchase['is_void'])
  );

  $onclick = $isVoid ? 'onclick="return false;"' : '';
  $formattedValue = number_format($purchase['value'], 2);

  echo <<<HTML
  <div class="card bg-base-100 shadow-2xl $classes" $dataAttrs $onclick>
    <div class="card-body">
      <h2 class="card-title w-full text-primary">{$purchase['name']}</h2>
      <ul class="mt-6 flex flex-col gap-2 text-xs">
        <li class="flex items-center">
          <div class="px-2 py-1 text-base-content inline-block opacity-85">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
            </svg>
          </div>
          <span class="font-bold px-2 py-1">{$purchase['payment_type']}</span>
        </li>
        <li class="flex items-center">
          <div class="px-2 py-1 text-base-content inline-block opacity-85">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
              </svg>
            </div>
            <span class="font-bold px-2 py-1">₱ {$formattedValue}</span>
        </li>
        <li class="flex items-center">
          <div class="px-2 py-1 text-base-content inline-block opacity-85">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
          </div>
          <span class="flex-1 font-bold px-2 py-1">{$purchase['date']}</span>
        </li>
        <li class="flex flex-col items-center justify-center">
          <p class="px-4 py-1 bg-primary/75 text-base-100 rounded-full text-xs font-light">
            Tap to see full details
          </p>
        </li>
      </ul>
    </div>
HTML;

  if ($isVoid) {
    echo <<<HTML
    <div class="z-20 card absolute top-0 left-0 h-full w-full flex items-center justify-center
      bg-primary opacity-80 select-none">
      <p class="leading-relaxed rotate-45 text-4xl font-bold uppercase pointer-events-none">
        VOIDED
      </p>
    </div>
HTML;
  }

  echo "</div>";
}
?>
