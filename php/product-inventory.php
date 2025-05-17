<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php'); 
require 'connection.php';

// Ensure all needed columns exist
$columns = ['status' => "VARCHAR(50) DEFAULT 'Available'", 'total_sold' => 'INT DEFAULT 0', 'available_stocks' => 'INT DEFAULT 0'];
foreach ($columns as $col => $type) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM product LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "ALTER TABLE product ADD $col $type");
    }
}

// Handle dropdown status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['status'])) {
    $id = intval($_POST['product_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE product SET status = '$status' WHERE id = $id");
}

// Handle search
$search = isset($_GET['searchProductName']) ? mysqli_real_escape_string($conn, $_GET['searchProductName']) : '';
$searchQuery = $search ? "WHERE name LIKE '%$search%'" : "";

// Fetch and update product inventory
$query = "
    SELECT p.*, 
           IFNULL((SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.product_name = p.name), 0) AS total_sold
    FROM product p
    $searchQuery
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Inventory</title>
    <link rel="stylesheet" type="text/css" href="../css/product-inventory.css">
    <link rel="icon" type="image/png" href="img/logo.png"/>
</head>
<body>

<div class="main-container">

    <div class="content-wrapper">
    <div class="all">

    <h1 class="text1">Product Inventory</h1>

    <div class="table-controls">
        <form method="GET" class="search-form">
            <input type="text" name="searchProductName" placeholder="Search product name" value="<?= htmlspecialchars($search) ?>" />
            <button class="btnSearch" type="submit">
                Search
            </button>
        </form>
        <button class="report-btn" onclick="window.location.href='inventory-report.php'">Generate Report</button>
    </div>
        <table cellspacing="0" cellpadding="10" class="viewTable">
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stocks</th>
                <th>Sold</th>
                <th>Available</th>
                <th>Status</th>
                <th>Update</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): 
                $sold = $row['total_sold'];
                $stock = $row['qty'];
                $available = max(0, $stock - $sold);

                // Update database with computed sold & available
                mysqli_query($conn, "UPDATE product SET total_sold = $sold, available_stocks = $available WHERE id = {$row['id']}");

                // Auto-set to "Out of Stock"
                if ($available == 0 && $row['status'] !== 'Out of Stock') {
                    mysqli_query($conn, "UPDATE product SET status = 'Out of Stock' WHERE id = {$row['id']}");
                    $row['status'] = 'Out of Stock';
                }
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $stock ?></td>
                <td><?= $sold ?></td>
                <td><?= $available ?></td>
                <td>
                    <form method="POST" style="display:flex;">
                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                        <select name="status" class="select-status">
                            <option value="Available" <?= $row['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Out of Stock" <?= $row['status'] == 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
                            <option value="Phase Out" <?= $row['status'] == 'Phase Out' ? 'selected' : '' ?>>Phase Out</option>
                        </select>
                </td>
                <td><button class="blockbtn" type="submit">Save</button></form></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    </div>
</body>
</html>