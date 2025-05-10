<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $cashier_name = $_POST['cashier_name'] ?? 'Admin';
    $cart_data = json_decode($_POST['cart_data'], true);

    if (!$cart_data || count($cart_data) === 0) {
        die("Cart is empty.");
    }

    // Calculate total
    $total_amount = 0;
    foreach ($cart_data as $item) {
        $total_amount += $item['price'] * $item['qty'];

        // Check stock
        $check = $conn->prepare("SELECT available_stocks FROM product WHERE id = ?");
        $check->bind_param("i", $item['id']);
        $check->execute();
        $result = $check->get_result();
        $row = $result->fetch_assoc();

        if (!$row || $row['available_stocks'] < $item['qty']) {
            die("❌ Insufficient stock for: " . $item['name']);
        }
    }

    // Insert into pos_orders
    $insertOrder = $conn->prepare("INSERT INTO pos_orders (cashier_name, total_amount, payment_method) VALUES (?, ?, ?)");
    $insertOrder->bind_param("sds", $cashier_name, $total_amount, $payment_method);
    $insertOrder->execute();
    $pos_order_id = $insertOrder->insert_id;

    // Insert into pos_order_items + update product
    $insertItem = $conn->prepare("INSERT INTO pos_order_items (pos_order_id, product_id, product_name, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $updateStock = $conn->prepare("UPDATE product SET available_stocks = available_stocks - ?, total_sold = total_sold + ? WHERE id = ?");

    foreach ($cart_data as $item) {
        $id = $item['id'];
        $name = $item['name'];
        $qty = $item['qty'];
        $price = $item['price'];
        $total_price = $price * $qty;

        $insertItem->bind_param("iisidd", $pos_order_id, $id, $name, $qty, $price, $total_price);
        $insertItem->execute();

        $updateStock->bind_param("iii", $qty, $qty, $id);
        $updateStock->execute();
    }

    echo "<script>alert('✅ POS Order Successful!'); window.location.href='pos.php';</script>";
    exit;
}
?>