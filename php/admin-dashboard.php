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
    <link rel="stylesheet" href="../css/admin-dashboard.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <title>Admin Dashboard - Sunny Bloom</title>
</head>
<body>
<div class="main-container">
  <div class="content-wrapper">
  <h1 class="text1">Dashboard</h1>
    <div class="all">

      <div class="dashboard-container">
        <?php
          $dbConnectionOrders = mysqli_connect("localhost:3306", "root", "", "flowershop");

          if (!$dbConnectionOrders) {
              die("Connection failed: " . mysqli_connect_error());
          }

          // Fetch total sales
          $selectTotalSalesQuery = "SELECT SUM(total_amount) AS total_sales FROM flowershop.orders";
          $totalSalesResult = mysqli_query($dbConnectionOrders, $selectTotalSalesQuery);
          $totalSalesData = mysqli_fetch_assoc($totalSalesResult);
          $totalSales = isset($totalSalesData['total_sales']) ? $totalSalesData['total_sales'] : 0;
        ?>
        <div class="dashboard-item">
          <h2>Total Sales</h2>
          <p>&#8369; <?php echo $totalSales; ?></p>
        </div>

        <?php
          // Fetch total items sold
          $selectTotalItemsSoldQuery = "SELECT COUNT(id) AS total_items_sold FROM flowershop.orders";
          $totalItemsSoldResult = mysqli_query($dbConnectionOrders, $selectTotalItemsSoldQuery);
          $totalItemsSoldData = mysqli_fetch_assoc($totalItemsSoldResult);
          $totalItemsSold = isset($totalItemsSoldData['total_items_sold']) ? $totalItemsSoldData['total_items_sold'] : 0;
        ?>
        <div class="dashboard-item">
          <h2>Items Sold</h2>
          <p><?php echo $totalItemsSold; ?></p>
        </div>

        <?php
          // Fetch total orders
          $selectTotalOrdersQuery = "SELECT COUNT(id) AS total_orders FROM flowershop.orders";
          $totalOrdersResult = mysqli_query($dbConnectionOrders, $selectTotalOrdersQuery);
          $totalOrdersData = mysqli_fetch_assoc($totalOrdersResult);
          $totalOrders = isset($totalOrdersData['total_orders']) ? $totalOrdersData['total_orders'] : 0;
        ?>
        <div class="dashboard-item">
          <h2>Total Orders</h2>
          <p><?php echo $totalOrders; ?></p>
        </div>

        <?php
          // Fetch total users
          $dbConnectionUsers = mysqli_connect("localhost:3306", "root", "", "flowershop");

          if (!$dbConnectionUsers) {
              die("Connection failed: " . mysqli_connect_error());
          }

          $selectTotalUsersQuery = "SELECT COUNT(id) AS total_users FROM flowershop.users";
          $totalUsersResult = mysqli_query($dbConnectionUsers, $selectTotalUsersQuery);
          $totalUsersData = mysqli_fetch_assoc($totalUsersResult);
          $totalUsers = isset($totalUsersData['total_users']) ? $totalUsersData['total_users'] : 0;
        ?>
        <div class="dashboard-item">
          <h2>Total Users</h2>
          <p><?php echo $totalUsers; ?></p>
        </div>
      </div>

      <?php
        // Fetch best selling items
        $selectBestSellingItemsQuery = "
          SELECT p.name, p.image, SUM(oi.quantity) AS total_quantity_sold
          FROM flowershop.order_items oi
          JOIN flowershop.product p ON oi.product_name = p.name
          GROUP BY p.id
          HAVING total_quantity_sold >= 3
          ORDER BY total_quantity_sold DESC
        ";
        $bestSellingItemsResult = mysqli_query($dbConnectionOrders, $selectBestSellingItemsQuery);
      ?>
      <div class="container-items">
        <div class="selling-item">
          <h1>Best Selling Items</h1>
          <div class="rows">
            <?php while ($row = mysqli_fetch_assoc($bestSellingItemsResult)) : ?>
              <div class="items">
                <img src="../img/<?= $row['image'] ?>" alt="<?= $row['name'] ?>" style="max-width: 100px; max-height: 100px;">
                <div><?= $row['name'] ?></div>
                <div><?= $row['total_quantity_sold'] ?> sold</div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>

        <?php
          // Fetch slow selling items
          $selectSlowSellingItemsQuery = "
            SELECT p.name, p.image, SUM(oi.quantity) AS total_quantity_sold
            FROM flowershop.order_items oi
            JOIN flowershop.product p ON oi.product_name = p.name
            GROUP BY p.id
            HAVING total_quantity_sold <= 2
            ORDER BY total_quantity_sold ASC
          ";
          $slowSellingItemsResult = mysqli_query($dbConnectionOrders, $selectSlowSellingItemsQuery);
        ?>
        <div class="selling-item">
          <h1>Slow Selling Items</h1>
          <div class="rows">
            <?php while ($row = mysqli_fetch_assoc($slowSellingItemsResult)) : ?>
              <div class="items">
                <img src="../img/<?= $row['image'] ?>" alt="<?= $row['name'] ?>" style="max-width: 100px; max-height: 100px;">
                <div><?= $row['name'] ?></div>
                <div><?= $row['total_quantity_sold'] ?> sold</div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>