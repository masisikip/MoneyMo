<?php
include_once '../includes/connect-db.php';
include_once '../includes/partial.php';

$low_stock_threshold = 10;

// Date Filter Parameters
$start_date = isset($_GET['start-date']) ? $_GET['start-date'] : null;
$end_date = isset($_GET['end-date']) ? $_GET['end-date'] : null;

// Fetch All Items
$stmt = $pdo->query("SELECT * FROM item");
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$item_lookup = [];
foreach ($items_raw as $item) {
    $id = $item["iditem"];
    $item_lookup[$id] = [
        "name" => $item["name"],
        "price" => (int) $item["value"],
        "stock" => (int) $item["stock"],
        "image" => $item["image"],
        "mime" => $item["mime"] ?? 'image/png'
    ];
}

// Get Low Stock Items
$low_stock_items = array_filter($item_lookup, function ($item) use ($low_stock_threshold) {
    return $item['stock'] > 0 && $item['stock'] <= $low_stock_threshold;
});

// Calculate Total Collected Cash and Item Stats
$total_collected_cash = 0;
$total_items_sold = 0;
$item_stats = [];

$query = "SELECT iditem, quantity FROM inventory WHERE is_received = 1";
$params = [];

// Add Date Filters
if ($start_date && $end_date) {
    $query .= " AND DATE(date) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $start_date;
    $params[':end_date'] = $end_date;
} elseif ($start_date) {
    $query .= " AND DATE(date) >= :start_date";
    $params[':start_date'] = $start_date;
} elseif ($end_date) {
    $query .= " AND DATE(date) <= :end_date";
    $params[':end_date'] = $end_date;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($transactions as $tx) {
    $id = $tx["iditem"];
    $qty = (int) $tx["quantity"];

    if (!isset($item_lookup[$id]))
        continue;

    $item = $item_lookup[$id];
    $subtotal = $item['price'] * $qty;

    if (!isset($item_stats[$id])) {
        $item_stats[$id] = [
            "name" => $item['name'],
            "price" => $item['price'],
            "total" => 0
        ];
    }

    $item_stats[$id]["total"] += $subtotal;
    $total_items_sold += $qty;
    $total_collected_cash += $subtotal;
}

// Pagination Setup
$limit = 15;

// Build Main Query with Date Filters
$base_query = "
    SELECT 
        reference_no,
        DATE(date) AS date,
        CONCAT(u1.f_name, ' ', u1.l_name) AS username,
        quantity,
        item.name AS itemname,
        item.value AS itemvalue,
        CONCAT(u2.f_name, ' ', u2.l_name) AS officerName,
        CASE 
            WHEN payment_type = 0 THEN 'Cash'
            WHEN payment_type = 1 THEN 'Gcash'
            ELSE 'unknown'
        END AS payment_type
    FROM inventory
    INNER JOIN item ON inventory.iditem = item.iditem
    INNER JOIN user u1 ON inventory.iduser = u1.iduser
    INNER JOIN user u2 ON inventory.idofficer = u2.iduser
";

$where_clauses = [];
$query_params = [];

if ($start_date) {
    $where_clauses[] = "DATE(date) >= :start_date";
    $query_params[':start_date'] = $start_date;
}
if ($end_date) {
    $where_clauses[] = "DATE(date) <= :end_date";
    $query_params[':end_date'] = $end_date;
}

if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$base_query .= " ORDER BY date DESC, ctrl_no DESC LIMIT :limit";

// Fetch Paginated Transactions
$stmt = $pdo->prepare($base_query);

foreach ($query_params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Total Pages with Date Filters
$count_query = "SELECT COUNT(*) FROM inventory";
$count_params = [];

if ($start_date || $end_date) {
    $count_where = [];
    if ($start_date) {
        $count_where[] = "DATE(date) >= :start_date";
        $count_params[':start_date'] = $start_date;
    }
    if ($end_date) {
        $count_where[] = "DATE(date) <= :end_date";
        $count_params[':end_date'] = $end_date;
    }
    $count_query .= " WHERE " . implode(" AND ", $count_where);
}

$stmt = $pdo->prepare($count_query);
$stmt->execute($count_params);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Build Query String for Pagination Links
$query_params = [];
if ($start_date)
    $query_params['start-date'] = $start_date;
if ($end_date)
    $query_params['end-date'] = $end_date;
$query_string = $query_params ? http_build_query($query_params) . '&' : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMo - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>

<body class="h-screen bg-gray-100">
    <?php include_once '../includes/partial.php'; ?>

    <main class="py-8 px-8 md:px-16 space-y-6">
        <!-- Date Filter Form -->
        <form method="GET" class="bg-white p-4 rounded-lg shadow">
            <div class="flex flex-col gap-2 md:gap-0 md:flex-row justify-between items-center">
                <h3 class="text-lg font-semibold">Filter Transactions</h3>
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex items-center gap-2">
                        <input type="date" name="start-date" value="<?= htmlspecialchars($start_date) ?>"
                            class="rounded border p-2 text-xs md:text-sm">
                        <span>to</span>
                        <input type="date" name="end-date" value="<?= htmlspecialchars($end_date) ?>"
                            class="rounded border p-2 text-xs md:text-sm">
                    </div>
                    <div class="flex justify-center md:justify-start gap-2">
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600 cursor-pointer">
                            Apply
                        </button>
                        <button type="button" onclick="window.location.href='?'"
                            class="bg-gray-100 px-4 py-2 rounded text-sm hover:bg-gray-200 cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Stats Container -->
        <div class="grid grid-cols-1 w-full md:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="md:col-span-1 bg-white p-4 rounded-lg shadow space-y-8">
                <!-- Total Collected -->
                <div class="text-center w-full md:w-fit">
                    <h3 class="text-lg text-gray-500 mb-2">Total Collected Cash</h3>
                    <p class="text-3xl font-bold">
                        ₱<?= number_format($total_collected_cash, 2) ?>
                    </p>
                </div>

                <!-- Low Stock Items -->
                <div>
                    <h3 class="text-lg text-gray-500 mb-4">Low Stock Items</h3>
                    <div class="space-y-4">
                        <?php foreach ($low_stock_items as $item): ?>
                            <div class="flex items-center gap-4 p-2 bg-gray-50 rounded">
                                <img src="data:<?= htmlspecialchars($item['mime']) ?>;base64,<?= base64_encode($item['image']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 object-cover rounded">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item['name']) ?></p>
                                    <p class="text-sm text-red-500">Stock: <?= $item['stock'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg shadow">
                <div class="mb-4">
                    <h3 class="text-lg text-gray-800">Collection Breakdown</h3>
                </div>
                <div class="w-full flex flex-col md:flex-row md:items-center gap-2 p-4 md:h-96">
                    <canvas id="chartCanvas" height="400"></canvas>
                    <div id="custom-legend" class="space-y-4 ml-4 w-full pr-8 grid grid-cols-1 place-content-center">
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white shadow rounded-lg p-4 md:p-6 overflow-x-auto">
            <h1 class="w-full mb-6 text-2xl font-bold">Recent Transactions</h1>
            <div class="min-w-full overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-4 text-left">Student Name</th>
                            <th class="py-2 px-4 text-left">Date</th>
                            <th class="py-2 px-4 text-left">Item</th>
                            <th class="py-2 px-4 text-left">Price</th>
                            <th class="py-2 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr class="border-b">
                                <td class="py-3 px-4"><?= htmlspecialchars($purchase['username']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($purchase['date']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($purchase['itemname']) ?></td>
                                <td class="py-3 px-4">₱<?= number_format($purchase['itemvalue'], 2) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <button class="bg-black text-white px-4 py-1 rounded-full hover:bg-gray-700 print-btn cursor-pointer">
                                        Print
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function generateGrayShades(count) {
                const colors = [];
                const step = 220 / Math.max(count - 1, 1);
                for (let i = 0; i < count; i++) {
                    const grayLevel = Math.round(60 + (step * i));
                    colors.push(`rgb(${grayLevel},${grayLevel},${grayLevel})`);
                }
                return colors;
            }

            function renderDonutChart(data) {
                const items = Object.values(data.items || {})
                    .sort((a, b) => b.total - a.total);

                const labels = items.map(i => i.name);
                const values = items.map(i => i.total);
                const sum = values.reduce((a, b) => a + b, 0);
                const colors = generateGrayShades(items.length);

                const ctx = document.getElementById('chartCanvas').getContext('2d');
                if (window.myChart) window.myChart.destroy();

                window.myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '50%',
                        layout: { // ✅ this should be directly under options
                            padding: {
                                top: 40,
                                bottom: 40,
                                left: 40,
                                right: 40
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const pct = ((ctx.parsed / sum) * 100).toFixed(1);
                                        return `${ctx.label}: ${pct}% (₱${ctx.parsed.toLocaleString()})`;
                                    }
                                }
                            },
                            datalabels: {
                                color: '#111',
                                formatter: (val) => ((val / sum) * 100).toFixed(1) + '%',
                                anchor: 'end',
                                align: 'end',
                                offset: 5,
                                clamp: true,
                                clip: false,
                                borderRadius: 4,
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // ✅ this should be a sibling to `options`
                });

                // Update Custom Legend
                const legendContainer = document.getElementById('custom-legend');
                legendContainer.innerHTML = items.map((item, index) => `
                <div class="flex items-center justify-between py-1">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full mr-2" style="background:${colors[index]}"></span>
                        <span>${item.name}</span>
                    </div>
                    <span class="font-medium">₱${item.total.toLocaleString()}</span>
                </div>
            `).join('');
            }

            // Initial Chart Render
            renderDonutChart({
                items: <?= json_encode(array_values($item_stats)) ?>
            });

            // Print Receipt Handler
            document.querySelectorAll('.print-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const row = e.target.closest('tr');
                    const data = {
                        studentName: row.children[0].textContent.trim(),
                        date: row.children[1].textContent.trim(),
                        item: row.children[2].textContent.trim(),
                        amount: row.children[3].textContent.trim(),
                    };

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                    <html>
                        <head>
                            <title>MoneyMo - Receipt</title>
                            <style>
                                body { font-family: Arial, sans-serif; padding: 20px; }
                                .receipt { width: 300px; margin: 0 auto; }
                                .header { text-align: center; margin-bottom: 20px; }
                                .details { margin-bottom: 15px; }
                                .total { font-weight: bold; margin-top: 10px; }
                            </style>
                        </head>
                        <body>
                            <div class="receipt">
                                <div class="header">
                                    <h2>Payment Receipt</h2>
                                    <p>Association of Computer Scientists</p>
                                </div>
                                <div class="details">
                                    <p>Student: ${data.studentName}</p>
                                    <p>Date: ${data.date}</p>
                                    <p>Item: ${data.item}</p>
                                    <p>Amount: ${data.amount}</p>
                                </div>
                                <div class="total">
                                    Total Paid: ${data.amount}
                                </div>
                            </div>
                        </body>
                    </html>
                `);
                    printWindow.document.close();
                    printWindow.print();
                });
            });
        });

        $(document).ready(function () {
            $('#header-title').text('Dashboard');
        })
    </script>
</body>

</html>