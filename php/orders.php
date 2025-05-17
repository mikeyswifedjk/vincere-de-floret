<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="icon" type="image/png" href="img/logo.png"/>
    <link rel="stylesheet" href="../css/orders.css">
    <style>
    </style>
</head>
<body>

<div class="main-container">

    <div class="content-wrapper">
    <div class="all">
        <h1 class="text1">Orders History</h1>

        <!-- Report Form -->
        <form method="GET" action="orders-report.php" class="date-report">
            <label for="start-date" class="text5">Start Date:</label>
            <input type="date" name="start-date" id="start-date" required />
            
            <label for="end-date" class="text5">End Date:</label>
            <input type="date" name="end-date" id="end-date" required />
            
            <input class="search-button" type="submit" name="search-btn" id="search-btn" value="Report">
        </form>
        <!-- Display Orders Table -->
        <table cellspacing="0" cellpadding="10" class="viewTable">
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Amount Paid</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
            <?php
            require 'connection.php';

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Fetch data from the "Orders" table
            $selectOrdersQuery = "SELECT * FROM orders";
            $ordersResult = mysqli_query($conn, $selectOrdersQuery);

            if ($ordersResult) {
                while ($order = mysqli_fetch_assoc($ordersResult)) {
                    echo "<tr>";
                    echo "<td>" . $order['id'] . "</td>";
                    echo "<td>" . $order['user_name'] . "</td>";
                    echo "<td>" . $order['name'] . "</td>";
                    echo "<td>" . $order['phone'] . "</td>";
                    echo "<td>" . $order['address'] . "</td>";
                    echo "<td>" . $order['payment_method'] . "</td>";
                    echo "<td>₱" . $order['total_amount'] . "</td>";
                    echo "<td>₱" . $order['total_amount'] . "</td>";
                    echo "<td>" . $order['order_date'] . "</td>";
                    echo "<td>" . $order['status'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No orders found</td></tr>";
            }

            mysqli_free_result($ordersResult);

            ?>
        </table>

        <h1 class="text1">Order Items</h1>
        <!-- Display Order Items Table -->
        <table cellspacing="0" cellpadding="10" class="viewTable">
           
            <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
                <th>Product Image</th>
            </tr>
            <?php
            // Fetch data from the "order_items" table
            $selectOrderItemsQuery = "SELECT * FROM order_items";
            $orderItemsResult = mysqli_query($conn, $selectOrderItemsQuery);

            if ($orderItemsResult) {
                while ($item = mysqli_fetch_assoc($orderItemsResult)) {
                    echo "<tr>";
                    echo "<td>" . $item['id'] . "</td>";
                    echo "<td>" . $item['order_id'] . "</td>";
                    echo "<td>" . $item['product_name'] . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>₱" . $item['price'] . "</td>";
                    echo "<td>₱" . $item['total_price'] . "</td>";
                    echo "<td><img src='../img/" . $item['product_image'] . "' alt='Product Image' height='50px'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No order items found</td></tr>";
            }

            mysqli_free_result($orderItemsResult);
            mysqli_close($conn);
            ?>
        </table>
    </div>
    </div>
    </div>
</body>
</html>
