<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} else {
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/flowershop/php/login.php");
    exit;
}

// Process checkout for selected items
if (isset($_POST['checkout_items'])) {
    if (!empty($_POST['selected_items'])) {
        // selected_items should be an array from checkboxes, so encode it properly
        $_SESSION['selected_items'] = $_POST['selected_items'];
        header("Location: checkout.php?user=" . urlencode($userName));
        exit;
    } else {
        echo "<script>alert('Please select items to checkout first.'); window.location.href='cart.php?user=" . urlencode($userName) . "';</script>";
        exit;
    }
}

// If you want to log out, you can add a condition to check for a logout action
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/flowershop/php/customer-landing-page.php");
    exit;
}

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

// Fetch updated cart count
$cartCountQuery = "SELECT COUNT(*) AS count FROM cart WHERE user_id = ?";
$cartCountStatement = mysqli_prepare($conn, $cartCountQuery);
mysqli_stmt_bind_param($cartCountStatement, "i", $user_id);
mysqli_stmt_execute($cartCountStatement);
$cartCountResult = mysqli_stmt_get_result($cartCountStatement);

if (!$cartCountResult) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$cartCountRow = mysqli_fetch_assoc($cartCountResult);
$cartCount = isset($cartCountRow['count']) ? $cartCountRow['count'] : 0;

if (isset($_POST['delete_items'])) {
    if (!empty($_POST['selected_items'])) {
        $selectedItems = array_filter(explode(",", $_POST['selected_items']), 'is_numeric');
        foreach ($selectedItems as $selectedItem) {
            // Process each selected item individually
            $selectedItem = intval($selectedItem);

            $deleteQuery = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $deleteStatement = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($deleteStatement, "ii", $selectedItem, $user_id);
            $deleteResult = mysqli_stmt_execute($deleteStatement);

            if (!$deleteResult) {
                die("Error in SQL query: " . mysqli_error($conn));
            }
        }

        header("Location: cart.php?user=$userName");
        exit;
    }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/cart.css" />
    <title>CART</title>
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
        <button class="dropbtn">Welcome, <?= htmlspecialchars($userName) ?> &#9662;</button>
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

    <!-- Display cart -->
    <?php
    echo '<div class="cart-container">';
    echo '<div class="row-fields">';
    echo '<p></p>';
    echo '<p>Product</p>';
    echo '<p>Quantity</p>';
    echo '<p>Unit Price</p>';
    echo '<p>Total Price</p>';
    echo '<p>Action</p>';
    echo '</div>';
    $totalQuantity = 0;
    $totalPrice = 0;

    // Display cart items
    $cartItemsQuery = "SELECT c.id, c.product_name, c.quantity, p.price, p.image, p.id AS product_id
                        FROM cart c
                        JOIN product p ON c.product_id = p.id
                        WHERE c.user_id = ?";
    $cartItemsStatement = mysqli_prepare($conn, $cartItemsQuery);
    mysqli_stmt_bind_param($cartItemsStatement, "i", $user_id);
    mysqli_stmt_execute($cartItemsStatement);
    $cartItemsResult = mysqli_stmt_get_result($cartItemsStatement);

    if (!$cartItemsResult) {
        die("Error in SQL query: " . mysqli_error($conn));
    }

    // Initialize total variables
    $totalQuantity = 0;
    $totalPrice = 0;

    while ($cartItem = mysqli_fetch_assoc($cartItemsResult)):
        $quantity = $cartItem['quantity'];
        $price = $cartItem['price'];
        $totalQuantity += $quantity;
        $totalPrice += ($quantity * $price);

        ?>
        <div class="items">
            <input type="checkbox" name="selected_items[]" value="<?php echo $cartItem['id']; ?>" class="box">
            <p class="prod-name"><?php echo $cartItem['product_name']; ?></p>
            <p style="display:none;">Quantity: <?php echo $quantity; ?></p>
            <p class="tPrice">₱ <?php echo $price; ?></p>
            <p style="position:relative; left:15%">₱ <?php echo $quantity * $price; ?></p>
            <img src="../img/<?php echo $cartItem['image']; ?>" alt="<?php echo $cartItem['product_name']; ?>" class="img-prod" height="90" width="90" />

            <form method="post" class="forms" action="update-cart.php" id="update_form_<?php echo $cartItem['id']; ?>">
                <input type="hidden" name="user" value="<?php echo $userName; ?>">
                <input type="hidden" name="cart_id" value="<?php echo $cartItem['id']; ?>">
                <input type="number" class="quantity-txt" name="new_quantity" id="new_quantity_<?php echo $cartItem['id']; ?>" value="<?php echo $quantity; ?>" min="1">

                <button type="submit"><i class="fa-solid fa-pen-to-square" style="color: #ffffff;"></i></button>
            </form>
        </div>
    <?php endwhile;

    echo '</div>';
    ?>
    <div class="total-container">
           <!-- Display total quantity and total price dynamically using JavaScript -->
    <span id="totalQuantity">Total Quantity: <?php echo $totalQuantity; ?></span>
    <span id="totalPrice">Total Amount: <?php echo $totalPrice; ?></span>

    <form id="cartForm" method="post" action="cart.php?user=<?php echo $userName; ?>">
        <button class="delete-button" type="submit" name="delete_items">Delete Items</button>
        <button type="submit" name="checkout_items">Checkout Items</button>
        <input type="hidden" id="item-ids" name="selected_items">   
    </form>                 
    </div>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete the selected items?");
        }

        function setItems() {
            const items = document.getElementsByClassName("items");
            let itemIds = "";
            let index = 0;
            for (const item of items) {
                const itemBox = item.firstElementChild;
                if (itemBox.checked) {
                    if (index == 0){
                        itemIds += itemBox.value;
                    }else {
                        itemIds += "," + itemBox.value;
                    }
                }
                index++;
            }
            return itemIds;
        }

        document.getElementById('cartForm').addEventListener('submit', function (event) {
            const deleteButton = document.querySelector('button[name="delete_items"]');
            const checkoutButton = document.querySelector('button[name="checkout_items"]');

            // Check if the clicked button is the "Checkout Selected" button
            if (event.submitter === deleteButton) {
                // For other buttons (e.g., "Delete Selected"), show the confirmation dialog
                if (confirmDelete()) {
                    document.getElementById("item-ids").value = setItems();
                } else {
                    event.preventDefault(); // Prevents the default form submission if the user cancels the confirmation
                }
            }else if (event.submitter === checkoutButton) {
                document.getElementById("item-ids").value = setItems();
            }
        });

        function updateTotals() {
            let checkboxes = document.getElementsByName('selected_items[]');
            totalQuantity = 0;
            totalPrice = 0;

            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    let parentDiv = checkbox.closest('.items');
                    let quantity = parseInt(parentDiv.querySelector('.quantity-txt').value);
                    let price = parseFloat(parentDiv.querySelector('.tPrice').innerText.replace('₱ ', ''));
                    totalQuantity += quantity;
                    totalPrice += quantity * price;
                }
            });

            document.getElementById('totalQuantity').innerText = 'Total Quantity: ' + totalQuantity;
            document.getElementById('totalPrice').innerText = 'Total Amount: ₱ ' + totalPrice.toFixed(2);
        }


        // Reattach the updateTotals function to the change event of checkboxes after changes in HTML structure
        function attachUpdateEventListeners() {
            let checkboxes = document.getElementsByName('selected_items[]');
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateTotals);
            });
        }

        // Initialize totals and attach event listeners when the page loads
        updateTotals();
        attachUpdateEventListeners();
    </script>
</body>
</html>
<?php
include 'footer.php';
?>