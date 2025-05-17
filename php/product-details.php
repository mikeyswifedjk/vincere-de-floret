<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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

// Fetch product details based on the product ID from the URL parameter
$productId = isset($_GET['id']) ? $_GET['id'] : 0;
$query = "SELECT * FROM product WHERE id = $productId";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

// Get product details
$product = mysqli_fetch_assoc($result);

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

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/product-details.css" />
    <title>Product Details</title>
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
    <!-- Header Content -->
    <a href="customer-dashboard.php?user=<?php echo $userName; ?>" class="container-header">
            <img class="logo" src="../img/<?php echo basename($logoPath); ?>" alt="Logo">
            <label class="shop"><?php echo $shopName; ?></label>
    </a>

    <!-- Search Bar -->
    <div class="content-search">
        <input type="text" class="search-bar" placeholder="Search products..."/>
        <button class="search-button">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

      <!-- Right Side: Cart and Profile Settings -->
  <div class="header-right">
    <!-- Cart Buttons -->
    <a href="cart.php?user=<?php echo $userName; ?>" class="cart-link">
            <button class="cart-button">
                <i class="fas fa-shopping-cart"></i>
                <?php
                require 'connection.php';

                $userQuery = "SELECT id FROM users WHERE name = ?";
                $userStatement = mysqli_prepare($conn, $userQuery);
                mysqli_stmt_bind_param($userStatement, "s", $userName);
                mysqli_stmt_execute($userStatement);
                $userResult = mysqli_stmt_get_result($userStatement);
                        
                if (!$userResult) {
                    die("Error in SQL query: " . mysqli_error($conn));
                }
                
                $userRow = mysqli_fetch_assoc($userResult);
                $user_id = isset($userRow['id']) ? $userRow['id'] : 0;

                // Fetch the cart count for the current user
                $cartCountQuery = "SELECT COUNT(*) AS count FROM cart WHERE user_id = ?";
                $cartCountStatement = mysqli_prepare($conn, $cartCountQuery);

                if ($cartCountStatement) {
                    mysqli_stmt_bind_param($cartCountStatement, "i", $user_id);
                    mysqli_stmt_execute($cartCountStatement);
                    $cartCountResult = mysqli_stmt_get_result($cartCountStatement);

                    if ($cartCountResult) {
                        $cartCountRow = mysqli_fetch_assoc($cartCountResult);
                        $cartCount = isset($cartCountRow['count']) ? $cartCountRow['count'] : "0";

                        // Display the cart number
                        echo "<span class='cart-number'>$cartCount</span>";
                    }

                    mysqli_stmt_close($cartCountStatement);
                }
                ?>
            </button>
        </a>

    <!-- Navigation Links with Dropdown -->
    <nav class="nav-right">
        <div class="dropdown">
            <button class="dropbtn"><?php echo $userName; ?> &#9662;</button>
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

      <!-- Categories Section -->
  <section class="content-categories">
    <div class="categories-title"><p>CATEGORIES</p></div>
    <div class="containers-category">
                    <?php
                    require 'connection.php';

                    // Check connection
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    // Fetch categories from the database
                    $query = "SELECT * FROM category";
                    $result = mysqli_query($conn, $query);

                    // Check if the query was successful
                    if (!$result) {
                        die("Error in SQL query: " . mysqli_error($conn));
                    }

                    // Loop through the categories and display only the title
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Create a link for each category that points to the customer dashboard
                        $categoryLink = "product-category.php?category=" . urlencode($row['category']) . "&user=" . urlencode($userName);
                        echo "<a class='category-link' href='$categoryLink'>";
                        echo "<div class='categories'>";
                        echo "<div class='category-title'>";
                        echo "<span>{$row['category']}</span>";
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }

                    // Close the database connection
                    mysqli_close($conn);
                    ?>
                </div>
        </section>

    <!-- Display product details -->
    <div class="all">
    <img src="../img/<?php echo isset($product['image']) ? $product['image'] : ''; ?>" alt="<?php echo isset($product['name']) ? $product['name'] : ''; ?>" />

    <div class="details">
        <h1><?php echo isset($product['name']) ? $product['name'] : ''; ?></h1>
        <p class="peso">₱ <span id="priceRange"><?php echo isset($product['price']) ? $product['price'] : ''; ?></span></p>
        <p class="qty">Qty: <span id="productQty"><?php echo isset($product['available_stocks']) ? $product['available_stocks'] : ''; ?></span></p>
        <button class="add" onclick="addToCart(<?php echo $productId; ?>, '<?php echo $product['name']; ?>', '<?php echo $userName; ?>')">Add to Cart</button>
    </div>
    </div>

        <script>
            function addToCart(productId, productName, userName) {
                const quantity = 1; // Default quantity since no variant selection

                // Redirect to cart page with basic info only
                window.location.href = 'add-to-cart.php?id=' + productId +
                    '&name=' + encodeURIComponent(productName) +
                    '&quantity=' + quantity +
                    '&user=' + encodeURIComponent(userName);
            }
        </script>
</body>
</html>
<?php
include ('footer.php');
?>