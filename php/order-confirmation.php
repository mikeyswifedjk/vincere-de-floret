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

if (!isset($_SESSION['order_details'])) {
    header("Location: checkout.php");
    exit;
}

$orderDetails = $_SESSION['order_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <title>Order Confirmation</title>
    <link rel="stylesheet" type="text/css" href="../css/order-confirmation.css">
</head>
    <style>
        *{
        color: <?php echo $fontColor; ?>;
        }
        body {
        background-color: <?php echo $bgColor; ?>;
        }
    </style>
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

    <div class="confirmation-container">
        <h1>Order Confirmation</h1>
        <p >Thank you for your order!</p>
        <div class="order-summary">
            <p>Name: <?= htmlspecialchars($orderDetails['name']) ?></p>
            <p>Phone: <?= htmlspecialchars($orderDetails['phone']) ?></p>
            <p>Address: <?= htmlspecialchars($orderDetails['address']) ?></p>
            <p>Total Amount: ₱<?= number_format($orderDetails['total_amount'], 2) ?></p>
        </div>
        <h2>Order Details</h2>
        <div class="order-items">
            <?php
            require 'connection.php';

            $orderIdQuery = "SELECT id FROM orders WHERE user_name = ? ORDER BY id DESC LIMIT 1";
            $orderIdStmt = mysqli_prepare($conn, $orderIdQuery);
            mysqli_stmt_bind_param($orderIdStmt, "s", $_SESSION['user_name']);
            mysqli_stmt_execute($orderIdStmt);
            $orderIdResult = mysqli_stmt_get_result($orderIdStmt);
            $orderRow = mysqli_fetch_assoc($orderIdResult);
            $orderId = $orderRow['id'];

            $orderItemsQuery = "SELECT id, product_name, quantity, price, total_price FROM order_items WHERE order_id = ?";
            $orderItemsStmt = mysqli_prepare($conn, $orderItemsQuery);
            mysqli_stmt_bind_param($orderItemsStmt, "i", $orderId);
            mysqli_stmt_execute($orderItemsStmt);
            $orderItemsResult = mysqli_stmt_get_result($orderItemsStmt);

            while ($row = mysqli_fetch_assoc($orderItemsResult)) {
                echo '<div class="order-item">';
                echo '<p>Product: ' . htmlspecialchars($row['product_name']) . '</p>';
                echo '<p>Quantity: ' . htmlspecialchars($row['quantity']) . '</p>';
                echo '<p>Unit Price: ₱' . htmlspecialchars($row['price']) . '</p>';
                echo '<p>Total Price: ₱' . htmlspecialchars($row['total_price']) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
        <p>We will process your order soon. If you have any questions, please contact our customer service.</p>
    </div>
</body>
</html>
<?php
include 'footer.php';
?>