<?php
include_once "../../../includes/connect-db.php";

header("Content-Type: application/json");

try {
    $start_date = $_GET["start_date"] ?? null;
    $end_date = $_GET["end_date"] ?? null;

    $stmt = $pdo->query("SELECT * FROM item");
    $items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $item_lookup = [];
    foreach ($items_raw as $item) {
        $id = $item["iditem"];
        $item_lookup[$id] = [
            "name" => $item["name"],
            "price" => (int)$item["value"],
            "stock" => (int)$item["stock"],
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

    if ($start_date) {
        $query .= " AND received_at >= :start";
        $params[":start"] = $start_date;
    }

    if ($end_date) {
        $query .= " AND received_at <= :end";
        $params[":end"] = $end_date;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($transactions as $tx) {
        $id = $tx["iditem"];
        $qty = (int)$tx["quantity"];

        if (!isset($item_lookup[$id])) continue;

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

        if ($stock == 0) $out_of_stock_count++;
        elseif ($stock <= $low_stock_threshold) $low_stock_count++;

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

    $total_collected_cash = number_format($total_collected_cash, 2, '.', '');

    http_response_code(200);
    echo json_encode([
        "total_collected_cash" => $total_collected_cash,
        "total_items_sold" => $total_items_sold,
        "out_of_stock_count" => $out_of_stock_count,
        "low_stock_count" => $low_stock_count,
        "items" => $item_stats
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Server Error",
        "message" => $e->getMessage()
    ]);
}
