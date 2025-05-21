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

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Design settings
$sqlGetSettings = "SELECT * FROM design_settings WHERE id = 1";
$resultSettings = $conn->query($sqlGetSettings);
if ($resultSettings->num_rows > 0) {
    $row = $resultSettings->fetch_assoc();
    $bgColor = $row["background_color"];
    $fontColor = $row["font_color"];
    $shopName = $row["shop_name"];
    $logoPath = $row["logo_path"];
} else {
    $bgColor = "#ffffff";
    $fontColor = "#000000";
    $shopName = "Shop";
    $logoPath = "default.png";
}

// Fetch regions
$regions = [];
$regionResult = $conn->query("SELECT id, address, fee FROM shipping");
while ($row = $regionResult->fetch_assoc()) {
    $regions[] = $row;
}

// Fetch discounts
$discounts = [];
$discountResult = $conn->query("SELECT code, amount FROM discounts WHERE status = 'active'");
while ($row = $discountResult->fetch_assoc()) {
    $discounts[] = $row;
}

$selectedItems = isset($_SESSION['selected_items']) ? array_filter(explode(",", $_SESSION['selected_items']), 'is_numeric') : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $regionId = $_POST['region'];
    $discountCode = $_POST['discount_code'];
    $paymentMethod = $_POST['payment_method'];

    // Shipping fee
    $shippingQuery = "SELECT fee FROM shipping WHERE id = ?";
    $shippingStmt = mysqli_prepare($conn, $shippingQuery);
    mysqli_stmt_bind_param($shippingStmt, "i", $regionId);
    mysqli_stmt_execute($shippingStmt);
    $shippingResult = mysqli_stmt_get_result($shippingStmt);
    $shippingRow = mysqli_fetch_assoc($shippingResult);
    $shippingFee = isset($shippingRow['fee']) ? $shippingRow['fee'] : 0;

    // Discount
    $discountQuery = "SELECT amount FROM discounts WHERE code = ? AND status = 'active' LIMIT 1";
    $discountStmt = mysqli_prepare($conn, $discountQuery);
    mysqli_stmt_bind_param($discountStmt, "s", $discountCode);
    mysqli_stmt_execute($discountStmt);
    $discountResult = mysqli_stmt_get_result($discountStmt);
    $discountRow = mysqli_fetch_assoc($discountResult);
    $discountAmount = isset($discountRow['amount']) ? $discountRow['amount'] : 0;

    // Calculate total
    $totalAmount = 0;
    foreach ($selectedItems as $itemId) {
        $cartItemQuery = "SELECT quantity, price FROM cart WHERE id = ?";
        $cartItemStmt = mysqli_prepare($conn, $cartItemQuery);
        mysqli_stmt_bind_param($cartItemStmt, "i", $itemId);
        mysqli_stmt_execute($cartItemStmt);
        $cartItemResult = mysqli_stmt_get_result($cartItemStmt);
        $cartItemRow = mysqli_fetch_assoc($cartItemResult);
        $totalAmount += $cartItemRow['quantity'] * $cartItemRow['price'];
    }
    $totalAmount += $shippingFee;
    $totalAmount -= $discountAmount;

    // Session order data
    $_SESSION['order_details'] = [
        'selected_items' => $selectedItems,
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'region_id' => $regionId,
        'discount_code' => $discountCode,
        'payment_method' => $paymentMethod,
        'total_amount' => $totalAmount,
        'product_images' => []
    ];

    foreach ($selectedItems as $itemId) {
        $cartItemImageQuery = "SELECT product_image FROM cart WHERE id = ?";
        $cartItemImageStmt = mysqli_prepare($conn, $cartItemImageQuery);
        mysqli_stmt_bind_param($cartItemImageStmt, "i", $itemId);
        mysqli_stmt_execute($cartItemImageStmt);
        $cartItemImageResult = mysqli_stmt_get_result($cartItemImageStmt);
        $cartItemImageRow = mysqli_fetch_assoc($cartItemImageResult);
        $_SESSION['order_details']['product_images'][$itemId] = $cartItemImageRow['product_image'];
    }

    $customLetterPath = $_SESSION['custom_letter_path'] ?? null;

    $insertOrder = $conn->prepare("INSERT INTO orders (user_name, name, phone, address, region_id, discount_code, payment_method, total_amount, custom_letter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertOrder->bind_param("sssssssss", $userName, $name, $phone, $address, $regionId, $discountCode, $paymentMethod, $totalAmount, $customLetterPath);
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

        $total_price = $item['price'] * $item['quantity'];

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

          <label for="region" style="display: block; margin-bottom: 5px;">Region:</label>
            <select name="region" id="region" required style="width: 100%; padding: 12px 16px; border: 2px solid #cbb4d4; border-radius: 10px;">
              <option value="" disabled selected>Select a region</option>
              <?php foreach ($regions as $region) : ?>
                <option value="<?= $region['id'] ?>"><?= $region['address'] ?> (₱<?= $region['fee'] ?>)</option>
              <?php endforeach; ?>
            </select>
        </div>
      </div>

      <div class="discount-promo">
        <h2>PROMO &amp; DISCOUNT </h2>
        <div class="discount-section">
          <select name="discount_code" id="discount_code" style="width: 100%; padding: 12px 16px; border: 2px solid #cbb4d4; border-radius:10px;">
            <option value="">None</option>
            <?php foreach ($discounts as $discount) : ?>
              <option value="<?= $discount['code'] ?>"><?= $discount['code'] ?> (₱<?= $discount['amount'] ?>)</option>
            <?php endforeach; ?>
          </select>
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
          <p>Sub total: ₱<span id="subtotal"><?= $totalPrice ?></span></p>
          <p>Shipping Fee: ₱<span id="shipping_fee">0</span></p>
          <p>Discount: ₱<span id="discount_amount">0</span></p>
          <p style="color: red;">Total Amount: ₱<span id="total_amount"><?= $totalPrice ?></span></p>
        </div>
      </div>

      <button type="submit" class="place-order">Place Order</button>
    </form>
  </div>

  <script>
        document.getElementById('region').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var shippingFee = parseFloat(selectedOption.text.match(/\(₱(\d+(\.\d{1,2})?)\)/)[1]);
            document.getElementById('shipping_fee').textContent = shippingFee.toFixed(2);
            updateTotalAmount();
        });

        document.getElementById('discount_code').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var discountAmount = selectedOption.value ? parseFloat(selectedOption.text.match(/\(₱(\d+(\.\d{1,2})?)\)/)[1]) : 0;
            document.getElementById('discount_amount').textContent = discountAmount.toFixed(2);
            updateTotalAmount();
        });

        function updateTotalAmount() {
            var subtotal = parseFloat(document.getElementById('subtotal').textContent);
            var shippingFee = parseFloat(document.getElementById('shipping_fee').textContent);
            var discountAmount = parseFloat(document.getElementById('discount_amount').textContent);
            var totalAmount = subtotal + shippingFee - discountAmount;
            document.getElementById('total_amount').textContent = totalAmount.toFixed(2);
        }
    </script>
</body>
</html>
<?php
include 'footer.php';
?>