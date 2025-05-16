<?php
include_once '../../includes/connect-db.php';

$stmt = $pdo->query("SELECT * FROM item");
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$item_lookup = [];
foreach ($items_raw as $item) {
    $id = $item["iditem"];
    $item_lookup[$id] = [
        "name" => $item["name"],
        "price" => (int) $item["value"],
        "stock" => (int) $item["stock"],
    ];
}

$total_collected_cash = 0;
$total_items_sold = 0;
$out_of_stock_count = 0;
$low_stock_count = 0;
$low_stock_threshold = 15;
$item_stats = [];

$query = "SELECT iditem, quantity FROM inventory WHERE is_received = 1";
$params = [];

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($transactions as $tx) {
    $id = $tx["iditem"];
    $qty = (int) $tx["quantity"];

    if (!isset($item_lookup[$id]))
        continue;

    $name = $item_lookup[$id]["name"];
    $price = $item_lookup[$id]["price"];
    $stock = $item_lookup[$id]["stock"];
    $subtotal = $price * $qty;

    if (!isset($item_stats[$id])) {
        $item_stats[$id] = [
            "name" => $name,
            "price" => $price,
            "stock" => $stock,
            "sales" => 0,
            "total" => 0
        ];
    }

    $item_stats[$id]["sales"] += $qty;
    $item_stats[$id]["total"] += $subtotal;

    $total_items_sold += $qty;
    $total_collected_cash += $subtotal;
}

foreach ($item_lookup as $id => $data) {
    $stock = $data["stock"];

    if ($stock == 0)
        $out_of_stock_count++;
    elseif ($stock <= $low_stock_threshold)
        $low_stock_count++;

    if (!isset($item_stats[$id])) {
        $item_stats[$id] = [
            "name" => $data["name"],
            "price" => $data["price"],
            "stock" => $stock,
            "sales" => 0,
            "total" => 0
        ];
    }

    
    $item_stats[$id]["total"] = number_format($item_stats[$id]["total"], 2, '.', '');
}

usort($item_stats, function ($a, $b) {
    return $b['stock'] <=> $a['stock']; 
});
$total_collected_cash = number_format($total_collected_cash, 2, '.', '');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMo - Statistics</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="h-screen bg-gray-100">
    <?php
    include_once '../../includes/partial.php';
    ?>

    <main class="py-8 px-4 md:px-16 space-y-6">
        <!-- Date Picker Container -->
        <form id="date-filter-form">
            <div class="w-full flex justify-center md:justify-end gap-2">
                <div class="grid grid-cols-1 text-sm">
                    <label for="start-date">Start</label>
                    <input type="date" name="start-date" id="start-date"
                        class="rounded border border-gray-200 bg-white p-2 w-36 text-center">
                </div>
                <div class="grid grid-cols-1 text-sm">
                    <label for="end-date">End</label>
                    <input type="date" name="end-date" id="end-date"
                        class="rounded border border-gray-200 bg-white p-2 w-36 text-center">
                </div>
            </div>
        </form>

        <!-- Small Cards Container -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="mb-6">
                    <h3 class="text-gray-800">Total Collected Cash</h3>
                </div>
                <div class="grid place-items-center">
                    <span class="text-3xl font-bold"
                        id="total-collected-cash-value"><?php echo "P $total_collected_cash" ?></span>
                </div>
            </div>
            <div class="bg-white pt-4 px-6 pb-12 rounded-lg shadow">
                <div class="mb-6">
                    <h3 class="text-gray-800">Items Sold</h3>
                </div>
                <div class="grid place-items-center">
                    <span id="item-sold-value"
                        class="text-3xl font-bold"><?php echo ($total_items_sold > 1) ? "$total_items_sold items" : "$total_items_sold item" ?></span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="mb-6">
                    <h3 class="text-gray-800">Low Stock</h3>
                </div>
                <div class="grid place-items-center">
                    <span id="low-stock-value"
                        class="text-3xl font-bold"><?php echo ($low_stock_count > 1) ? "$low_stock_count items" : "$low_stock_count item" ?></span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="mb-6">
                    <h3 class="text-gray-800">Out of Stocks</h3>
                </div>
                <div class="grid place-items-center">
                    <span id="out-of-stock-value"
                        class="text-3xl font-bold"><?php echo ($out_of_stock_count > 1) ? "$out_of_stock_count items" : "$out_of_stock_count item" ?></span>
                </div>
            </div>
        </div>

        <!-- Big Cards Container -->
        <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Collection Breakdown -->
            <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg shadow min-h-80">
                <div>
                    <h3 class="text-lg text-gray-800">Collection Breakdown</h3>
                </div>
                <div class="flex flex-col md:flex-row gap-6 p-2">
                    <!-- Pie Chart -->
                    <div id="chartContainer" class="w-full max-w-xl mx-auto mt-4">
                        <canvas id="chartCanvas">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="w-40 my-12 mx-28">
                                <circle fill="none" stroke-opacity="1" stroke="#000000" stroke-width=".5" cx="100"
                                    cy="100" r="0">
                                    <animate attributeName="r" calcMode="spline" dur="2" values="1;80" keyTimes="0;1"
                                        keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                                    <animate attributeName="stroke-width" calcMode="spline" dur="2" values="0;25"
                                        keyTimes="0;1" keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                                    <animate attributeName="stroke-opacity" calcMode="spline" dur="2" values="1;0"
                                        keyTimes="0;1" keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                                </circle>
                            </svg>
                        </canvas>
                    </div>
                    <!-- Item Breakdown -->
                    <div id="custom-legend" class="space-y-4 w-full pr-8 grid grid-cols-1 place-content-center ">
                    </div>
                </div>
            </div>
            <!-- Item Stock Levels -->
            <div class="col-span-1 bg-white p-8 rounded-lg shadow min-h-80">
                <div class="mb-4">
                    <h3 class="text-lg text-gray-800 font-semibold">Item Stock Levels</h3>
                </div>
                <div id="stock-levels" class="pl-6 max-w-96 text-lg flex flex-col">
                    <?php foreach ($item_stats as $item): ?>
                        <?php $stock = (int) $item['stock']; ?>
                        <div class="flex justify-between py-2">
                            <span class="<?= $stock === 0 ? 'text-red-500' : ( $stock < 15 && $stock > 0 ? 'text-amber-600' : '') ?>"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="<?= $stock === 0 ? 'text-red-500' : ( $stock < 15 && $stock > 0 ? 'text-amber-600' : '') ?>"><?= $stock ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    <?php include_once '../../includes/footer.php'; ?>

</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(() => {
        $('#header-title').text('Statistics');

        const today = new Date().toISOString().split('T')[0];
        $('#start-date').attr('max', today);
        $('#end-date').attr('max', today);

        $.validator.addMethod("noFuture", function (value, element) {
            if (!value) return true;
            const today = new Date().setHours(0, 0, 0, 0);
            const inputDate = new Date(value).setHours(0, 0, 0, 0);
            return inputDate <= today;
        }, "Date cannot be in the future");

        $.validator.addMethod("dateOrder", function (value, element) {
            const start = $('#start-date').val();
            const end = $('#end-date').val();
            return !(start && end) || new Date(start) <= new Date(end);
        }, "Start date must be before or equal to end date");

        const validator = $('#date-filter-form').validate({
            rules: {
                'start-date': {
                    date: true,
                    noFuture: true,
                    dateOrder: true
                },
                'end-date': {
                    date: true,
                    noFuture: true,
                    dateOrder: true
                }
            },
            errorClass: "text-red-500 text-xs mt-1",
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });

        let debounceTimer;
        $('#start-date, #end-date').on('input', function () {
            $('#total-collected-cash-value').html(smallCardContainerLoader());
            $('#item-sold-value').html(smallCardContainerLoader());
            $('#low-stock-value').html(smallCardContainerLoader());
            $('#out-of-stock-value').html(smallCardContainerLoader());
            $('#chartContainer').html(`
            <div class="w-full flex justify-center py-12">
                ${smallCardContainerLoader()}
            </div>
            `);
            $('#custom-legend').html(itemStockLevelContainerLoader());
            $('#stock-levels').html(itemStockLevelContainerLoader());

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                if (!$('#date-filter-form').valid()) return;

                const start = $('#start-date').val();
                const end = $('#end-date').val();

                $.ajax({
                    url: 'api/statistics.php',
                    method: 'GET',
                    data: {
                        start_date: start,
                        end_date: end
                    },
                    dataType: 'json',
                    success: function (response) {
                        $('#total-collected-cash-value').text(`P ${response.total_collected_cash}`);
                        $('#item-sold-value').text(
                            response.total_items_sold > 1 ?
                                `${response.total_items_sold} items` :
                                `${response.total_items_sold} item`
                        );
                        $('#low-stock-value').text(
                            response.low_stock_count > 1 ?
                                `${response.low_stock_count} items` :
                                `${response.low_stock_count} item`
                        );
                        $('#out-of-stock-value').text(
                            response.out_of_stock_count > 1 ?
                                `${response.out_of_stock_count} items` :
                                `${response.out_of_stock_count} item`
                        );
                        renderDonutChart({
                            items: response.items
                        });
                        renderStockLevels(response.items);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }, 2000);
        });

        let smallCardContainerLoader = () => {
            return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="h-16">
                        <circle fill="none" stroke-opacity="1" stroke="#000000" stroke-width=".5" cx="100" cy="100" r="0">
                            <animate attributeName="r" calcMode="spline" dur="2" values="1;80" keyTimes="0;1" keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                            <animate attributeName="stroke-width" calcMode="spline" dur="2" values="0;25" keyTimes="0;1" keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                            <animate attributeName="stroke-opacity" calcMode="spline" dur="2" values="1;0" keyTimes="0;1" keySplines="0 .2 .5 1" repeatCount="indefinite"></animate>
                        </circle>
                    </svg>`
        }

        let itemStockLevelContainerLoader = () => {
            return `<div class="space-y-4 w-full pr-8">
                        <div class="flex gap-6">
                            <div class=" w-3/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                            <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                        <div class="flex gap-6">
                            <div class=" w-3/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                            <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                        <div class="flex gap-6">
                            <div class=" w-3/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                            <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                        <div class="flex gap-6">
                            <div class=" w-3/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                            <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                        <div class="flex gap-6">
                            <div class=" w-3/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                            <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
                        </div>
                    </div>`
        }

        function generateGrayShades(count) {
            const colors = [];
            const LIGHT = 220;
            const DARK = 60;
            const step = (LIGHT - DARK) / Math.max(count - 1, 1);

            for (let i = 0; i < count; i++) {
                const grayLevel = Math.round(DARK + (step * i));
                colors.push(`rgb(${grayLevel}, ${grayLevel}, ${grayLevel})`);
            }

            return colors;
        }

        function renderDonutChart(data) {
            const items = Object.values(data.items || {}).map(item => ({
                name: item.name,
                total: parseFloat(item.total)
            })).sort((a, b) => b.total - a.total);

            const labels = items.map(i => i.name);
            const values = items.map(i => i.total);
            const sum = values.reduce((a, b) => a + b, 0);
            const colors = generateGrayShades(items.length);

            $('#chartContainer').html(`<canvas id='chartCanvas'></chart>`);

            const ctx = document.getElementById('chartCanvas').getContext('2d');
            if (window.myChart) window.myChart.destroy();

            window.myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '50%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const label = ctx.label || '';
                                    const value = ctx.parsed;
                                    const pct = ((value / sum) * 100).toFixed(1);
                                    return `${label}: ${pct}%`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#111',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (val) => {
                                const pct = ((val / sum) * 100).toFixed(1);
                                return `${pct}%`;
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            renderCustomLegend(items, colors);
        }

        function renderCustomLegend(items, colors) {
            const $container = $('#custom-legend');
            $container.empty();

            items.forEach((item, index) => {
                const $row = $('<div>').addClass('flex items-center gap-2 max-w-96');
                const $color = $('<span>').addClass('w-2 h-6 rounded mr-2').css('background-color', colors[index]);
                const $label = $('<span>').text(item.name);
                const $amount = $('<span>')
                    .addClass('font-bold ml-auto')
                    .text(`₱${item.total.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}`);

                $row.append($color, $label, $amount);
                $container.append($row);
            });
        }

        function renderStockLevels(items) {
            const $container = $('#stock-levels');
            $container.empty();
            Object.values(items).forEach(item => {
                const $row = $('<div>').addClass('flex justify-between py-2');
                const $name = $('<span>').text(item.name);
                const $stock = $('<span>')
                    .text(item.stock)
                    .toggleClass('text-red-500', item.stock === 0);
                $row.append($name, $stock);
                $container.append($row);
            });
        }

        renderDonutChart({
            items: <?php echo json_encode($item_stats) ?>
        });
    })
</script>

</html>