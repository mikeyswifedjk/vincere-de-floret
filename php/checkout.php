<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} else {
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

// If you want to log out, you can add a condition to check for a logout action
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

$userName = $_SESSION['user_name'];
$selectedItems = isset($_SESSION['selected_items']) ? array_filter(explode(",", $_SESSION['selected_items']), 'is_numeric') : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $paymentMethod = $_POST['payment_method'];

    // Use only unit prices for total
    $totalAmount = 0;
    foreach ($selectedItems as $itemId) {
        $stmt = $conn->prepare("SELECT price FROM cart WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $totalAmount += $row['price'];
    }

    $_SESSION['order_details'] = [
        'selected_items' => $selectedItems,
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'payment_method' => $paymentMethod,
        'total_amount' => $totalAmount,
        'product_images' => []
    ];

    foreach ($selectedItems as $itemId) {
        $stmt = $conn->prepare("SELECT product_image FROM cart WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $_SESSION['order_details']['product_images'][$itemId] = $row['product_image'];
    }

    $customLetterPath = $_SESSION['custom_letter_path'] ?? null;

    $insertOrder = $conn->prepare("INSERT INTO orders (user_name, name, phone, address, payment_method, total_amount, custom_letter) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertOrder->bind_param("sssssds", $userName, $name, $phone, $address, $paymentMethod, $totalAmount, $customLetterPath);
    $insertOrder->execute();
    $orderId = $conn->insert_id;

    unset($_SESSION['custom_letter_path']);
    unset($_SESSION['custom_letter_html']);

    foreach ($selectedItems as $itemId) {
        $stmt = $conn->prepare("SELECT product_name, quantity, price FROM cart WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        $total_price = $item['price'];

        $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_name, product_image, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $insertItem->bind_param("isssdd", $orderId, $item['product_name'], $_SESSION['order_details']['product_images'][$itemId], $item['quantity'], $item['price'], $total_price);

        $insertItem->execute();
    }

    $clearCart = $conn->prepare("DELETE FROM cart WHERE user_id = (SELECT id FROM users WHERE name = ?) AND id IN (" . implode(',', array_map('intval', $selectedItems)) . ")");
    $clearCart->bind_param("s", $userName);
    $clearCart->execute();

    if ($paymentMethod === "GCash") {
        header("Location: gcash-payment.php");
    } elseif ($paymentMethod === "BDO") {
        header("Location: bdo-payment.php");
    } else {
        header("Location: order-confirmation.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/checkout.css" />
    <title>Checkout</title>
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

  <div class="checkout-container">
    <form id="checkout-form" method="POST" action="checkout.php">
      <div class="products">
        <h2>PRODUCT LIST</h2>
        <button type="button" class="create-custom-letter" onclick="window.location.href='custom-letter.php'">Create Custom Letter</button>
        <div class="cart-summary">
          <?php
            $totalPrice = 0;
            foreach ($selectedItems as $itemId) {
                $stmt = $conn->prepare("SELECT product_name, quantity, price, product_image FROM cart WHERE id = ?");
                $stmt->bind_param("i", $itemId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($row) {
                    $totalPrice += $row['price'];

                    echo '<div class="cart-item">';
                    echo '<img src="../img/' . $row['product_image'] . '" alt="' . $row['product_name'] . '" width="50" height="50">';
                    echo '<div class="cart-item-details">';
                    echo '<p class="product-name">Product: ' . $row['product_name'] . '</p>';
                    echo '<p class="qty">Quantity: ' . $row['quantity'] . '</p>';
                    echo '<p class="price">Unit Price: ₱' . $row['price'] . '</p>';
                    echo '</div></div>';
                } else {
                    echo '<p style="color:red;">Item with ID ' . htmlspecialchars($itemId) . ' not found in cart.</p>';
                }
            } 
          ?>
        </div>
      </div>

      <div class="shipping-details">
        <h2>SHIPPING DETAILS</h2>
        <div class="shipment-details">
          <label for="name">Full Name:</label>
          <input type="text" name="name" id="name" required />
          
          <label for="phone">Contact Number:</label>
          <input type="text" name="phone" id="phone" required />
          
          <label for="address">Complete Address:</label>
          <input type="text" name="address" id="address" required />
        </div>
      </div>

      <div class="payment-method">
        <h2>PAYMENT METHOD</h2>
        <div class="payment-options">
          <label>
            <input type="radio" name="payment_method" value="COD" required />
            <img src="../assets/shipping/cod.png" alt="COD" />
          </label>
          <label>
            <input type="radio" name="payment_method" value="BDO" />
            <img src="../assets/shipping/bdo.png" alt="BDO" />
          </label>
          <label>
            <input type="radio" name="payment_method" value="GCash" />
            <img src="../assets/shipping/gcash.png" alt="GCash" />
          </label>
        </div>
      </div>

      <div class="order-summary">
        <h2>ORDER SUMMARY</h2>
        <div class="order-summary-details">
            <p style="display: flex; justify-content: space-between; align-items: center;">
                <span>Shipping Fee:</span>
                <span style="color: #fff; font-weight: bold;">FREE SHIPPING</span>
            </p>
            <p style="display: flex; justify-content: space-between; align-items: center; color: red;">
                <span>Total Amount:</span>
                <span style="font-weight: bold;" id="total_amount">
                    <?php echo number_format($totalPrice, 2); ?>
                </span>
            </p>
        </div>
      </div>

      <button type="submit" class="place-order">Place Order</button>
    </form>
  </div>
</body>
</html>
<?php
include 'footer.php';
?>