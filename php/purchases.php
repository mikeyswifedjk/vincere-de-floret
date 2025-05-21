<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

$userName = $_SESSION['user_name'];

//settings for customer-design-settings
$sqlGetSettings = "SELECT * FROM design_settings WHERE id = 1";
$resultSettings = $conn->query($sqlGetSettings);

if ($resultSettings->num_rows > 0) {
    // Output data ng bawat row
    while ($row = $resultSettings->fetch_assoc()) {
        $bgColor = $row["background_color"];
        $fontColor = $row["font_color"];
        $shopName = $row["shop_name"];
        $logoPath = $row["logo_path"];
    }
} else {
    echo "0 results";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Purchases</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="icon" type="image/png" href="../assets/logo/logo2.png" />
  <link rel="stylesheet" href="../css/purchases.css">
  <style>
        *{
        color: <?php echo $fontColor; ?>;
        }
        body {
        background-color: <?php echo $bgColor; ?>;
        }
    </style>
</head>

<body>
<header class="header">
  <a href="customer-dashboard.php?user=<?= htmlspecialchars($userName) ?>" class="container-header">
    <img class="logo" src="../img/<?= htmlspecialchars(basename($logoPath)) ?>" alt="Sunny Blooms Logo" />
    <label class="shop"><?= htmlspecialchars($shopName) ?></label>
  </a>

  <!-- Search Bar -->
  <div class="content-search">
    <input type="text" class="search-bar" placeholder="Search products..." />
    <button class="search-button">
      <i class="fa-solid fa-magnifying-glass"></i>
    </button>
  </div>

  <!-- Right Side: Cart and Profile Settings -->
  <div class="header-right">
    <!-- Cart Button -->
    <a href="cart.php?user=<?= urlencode($userName) ?>" class="cart-link">
      <button class="cart-button">
        <i class="fas fa-shopping-cart"></i>
        <?php
          $userQuery = "SELECT id FROM users WHERE name = ?";
          $stmt = mysqli_prepare($conn, $userQuery);
          mysqli_stmt_bind_param($stmt, "s", $userName);
          mysqli_stmt_execute($stmt);
          $userResult = mysqli_stmt_get_result($stmt);
          $userRow = mysqli_fetch_assoc($userResult);
          $user_id = $userRow['id'] ?? 0;

          $cartQuery = "SELECT COUNT(*) AS count FROM cart WHERE user_id = ?";
          $cartStmt = mysqli_prepare($conn, $cartQuery);
          mysqli_stmt_bind_param($cartStmt, "i", $user_id);
          mysqli_stmt_execute($cartStmt);
          $cartResult = mysqli_stmt_get_result($cartStmt);
          $cartCount = mysqli_fetch_assoc($cartResult)['count'] ?? 0;
          echo "<span class='cart-number'>$cartCount</span>";
          mysqli_stmt_close($cartStmt);
        ?>
      </button>
    </a>

    <!-- User Dropdown -->
    <nav class="nav-right">
      <div class="dropdown">
        <button class="dropbtn"><?= htmlspecialchars($userName) ?> &#9662;</button>
        <div class="dropdown-content">
          <a href="user-profile-settings.php">Profile Settings</a>
          <a href="users-change-password.php">Password</a>
          <a href="purchases.php">My Purchases</a>
          <a href="?logout=1">Logout</a>
        </div>
      </div>
    </nav>
  </div>
</header>

<h1>PURCHASE HISTORY</h1>

<?php
$purchasesQuery = "
  SELECT o.id AS order_id, o.payment_method, oi.product_name, oi.product_image, oi.quantity, oi.price, oi.total_price, o.order_date
  FROM orders o
  JOIN order_items oi ON o.id = oi.order_id
  WHERE o.user_name = ?
  ORDER BY o.order_date DESC
";

$stmt = mysqli_prepare($conn, $purchasesQuery);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table cellpadding='10' cellspacing='0'>";
    echo "<tr>
            <th>Product</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Shipping Status</th>
            <th>Order Date</th>
          </tr>";

    while ($item = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
        echo "<td><img src='../img/" . htmlspecialchars($item['product_image']) . "' width='50'></td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>₱" . number_format($item['price'], 2) . "</td>";
        echo "<td>₱" . number_format($item['total_price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($item['payment_method']) . "</td>";
        echo "<td><span style='color: green;'>Approved</span></td>";
        echo "<td><span style='color: orange;'>To Ship</span></td>";
        echo "<td>" . $item['order_date'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No purchases found.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
</body>
</html>
<?php include('footer.php'); ?>