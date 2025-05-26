<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    require 'connection.php';

    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    $newShippingStatus = $_POST['shipping_status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ?, shipping_status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newStatus, $newShippingStatus, $orderId);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Optional: refresh the page to reflect changes immediately
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/orders.css">
    <style>
    </style>
</head>
<body>

<div class="main-container">

    <div class="content-wrapper">
    <div class="all">
        <h1 class="text1">Order Management</h1>
        <!-- Report Form -->
        <form method="GET" action="orders-report.php" class="date-report">
            <label for="start-date" class="text5">Start Date:</label>
            <input type="date" name="start-date" id="start-date" required />
            
            <label for="end-date" class="text5">End Date:</label>
            <input type="date" name="end-date" id="end-date" required />
            
            <input class="search-button" type="submit" name="search-btn" id="search-btn" value="Report">
        </form>

        <!-- Display Orders Table -->
        <br><h1 class="text1">Orders History</h1>
        <table cellspacing="0" cellpadding="10" class="viewTable">
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Sender Name</th>
                <th>Sender Phone</th>
                <th>Receiver Name</th>
                <th>Receiver Phone</th>
                <th>Address</th>
                <th>Payment Method</th>
                <th>Shipping Fee</th>
                <th>Discount Code</th>
                <th>Total Amount</th>
                <th>Amount Paid</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Shipping Status</th>
                <th>Action</th>
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
                    // Fetch the fee from the shipping table based on region_id
                    $regionId = $order['region_id'];
                    $shippingFee = 'N/A';

                    $shippingQuery = "SELECT fee FROM shipping WHERE id = '$regionId'";
                    $shippingResult = mysqli_query($conn, $shippingQuery);

                    if ($shippingResult && mysqli_num_rows($shippingResult) > 0) {
                        $shippingData = mysqli_fetch_assoc($shippingResult);
                        $shippingFee = "₱" . $shippingData['fee'];
                    }

                    echo "<tr>";
                    echo "<td>" . $order['id'] . "</td>";
                    echo "<td>" . $order['user_name'] . "</td>";
                    echo "<td>" . $order['sender_name'] . "</td>";
                    echo "<td>" . $order['sender_phone'] . "</td>";
                    echo "<td>" . $order['receiver_name'] . "</td>";
                    echo "<td>" . $order['receiver_phone'] . "</td>";
                    echo "<td>" . $order['address'] . "</td>";
                    echo "<td>" . $order['payment_method'] . "</td>";
                    echo "<td>" . $shippingFee . "</td>";
                    echo "<td>" . $order['discount_code'] . "</td>";
                    echo "<td>₱" . $order['total_amount'] . "</td>";
                    echo "<td>₱" . $order['total_amount'] . "</td>";
                    echo "<td>" . $order['order_date'] . "</td>";
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . $order['id'] . "'>";
                    echo "<td>
                            <select name='status'>
                                <option value='Under Review'" . ($order['status'] == 'Under Review' ? ' selected' : '') . ">Under Review</option>
                                <option value='Approved'" . ($order['status'] == 'Approved' ? ' selected' : '') . ">Approved</option>
                                <option value='Declined'" . ($order['status'] == 'Declined' ? ' selected' : '') . ">Declined</option>
                                <option value='Cancelled'" . ($order['status'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                            </select>
                        </td>";
                    echo "<td>
                            <select name='shipping_status'>
                                <option value='Pending'" . ($order['shipping_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                <option value='Processing'" . ($order['shipping_status'] == 'Processing' ? ' selected' : '') . ">Processing</option>
                                <option value='Out for Delivery'" . ($order['shipping_status'] == 'Out for Delivery' ? ' selected' : '') . ">Out for Delivery</option>
                                <option value='Delivered'" . ($order['shipping_status'] == 'Delivered' ? ' selected' : '') . ">Delivered</option>
                            </select>
                        </td>";
                    echo "<td><button type='submit' name='update_status'>Update</button></td>";
                    echo "</form>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No orders found</td></tr>";
            }
            mysqli_free_result($ordersResult);

            ?>
        </table>

        <br><h1 class="text1">Order Items</h1>
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

        <!-- Display POS Orders Table -->
        <br><h1 class="text1">POS History</h1>
        <table cellspacing="0" cellpadding="10" class="viewTable">
            <tr>
                <th>ID</th>
                <th>Cashier</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Order Date</th>
            </tr>
            <?php
            require 'connection.php';

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Fetch data from the "Orders" table
            $selectOrdersQuery = "SELECT * FROM pos_orders";
            $ordersResult = mysqli_query($conn, $selectOrdersQuery);

            if ($ordersResult) {
                while ($order = mysqli_fetch_assoc($ordersResult)) {
                    echo "<tr>";
                    echo "<td>" . $order['id'] . "</td>";
                    echo "<td>" . $order['cashier_name'] . "</td>";
                    echo "<td>" . $order['total_amount'] . "</td>";
                    echo "<td>" . $order['payment_method'] . "</td>";
                    echo "<td>" . $order['order_date'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No orders found</td></tr>";
            }
            mysqli_free_result($ordersResult);

            ?>
        </table>

        <br><h1 class="text1">POS Items</h1>
        <!-- Display Order Items Table -->
        <table cellspacing="0" cellpadding="10" class="viewTable">
           
            <tr>
                <th>ID</th>
                <th>POS Order ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
            <?php
            // Fetch data from the "order_items" table
            $selectOrderItemsQuery = "SELECT * FROM pos_order_items";
            $orderItemsResult = mysqli_query($conn, $selectOrderItemsQuery);

            if ($orderItemsResult) {
                while ($item = mysqli_fetch_assoc($orderItemsResult)) {
                    echo "<tr>";
                    echo "<td>" . $item['id'] . "</td>";
                    echo "<td>" . $item['pos_order_id'] . "</td>";
                    echo "<td>" . $item['product_name'] . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>₱" . $item['price'] . "</td>";
                    echo "<td>₱" . $item['total_price'] . "</td>";
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
