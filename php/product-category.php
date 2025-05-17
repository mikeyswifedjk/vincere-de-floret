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
    <link rel="stylesheet" href="../css/product-category.css" />
    <title>Product Cateogry</title>
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
   
        <!-- Daily discover content -->
<section class="daily-discover-content" id="product">
  <div class="daily-discover-title">
    <label>FILTER BY</label>
    <a href="product-category.php?category=<?= urlencode($_GET['category']) ?>&filter=all&user=<?= $userName ?>">
      <button name="all">ALL</button>
    </a>
    <a href="product-category.php?category=<?= urlencode($_GET['category']) ?>&filter=latest&user=<?= $userName ?>">
      <button name="latest">LATEST</button>
    </a>
  </div>

  <div class="daily-discover-container">
    <div class="grid-items">
      <?php
        require 'connection.php';

        if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
        }

        $itemsPerPage = 6;
        $filterType = $_GET['filter'] ?? 'all';
        $category = isset($_GET['category']) ? urldecode($_GET['category']) : '';
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $itemsPerPage;

        switch ($filterType) {
          case 'all':
            $query = "SELECT * FROM product WHERE category = '$category' LIMIT $offset, $itemsPerPage";
            break;
          case 'latest':
            $query = "SELECT * FROM product WHERE category = '$category' ORDER BY id DESC LIMIT 6";
            break;
          case 'topsale':
            $query = "SELECT p.* 
                      FROM product p
                      JOIN (
                          SELECT product_id, SUM(quantity) AS total_sales
                          FROM orders o
                          JOIN product p ON o.product_id = p.id
                          WHERE p.category = '$category'
                          GROUP BY product_id
                          ORDER BY total_sales DESC
                          LIMIT 6
                      ) o ON p.id = o.product_id";
            break;
          default:
            $query = "";
            break;
        }

        $result = mysqli_query($conn, $query);
        if (!$result) die("SQL Error: " . mysqli_error($conn));

        while ($product = mysqli_fetch_assoc($result)):
          $productName = htmlspecialchars($product['name']);
          $productPrice = number_format($product['price'], 2);
          $productImage = htmlspecialchars($product['image']);
      ?>
        <a class="product-link" href="product-details.php?id=<?= $product['id'] ?>">
          <div class="items">
            <img src="../img/<?= $productImage ?>" alt="<?= $productName ?>" />
            <div class="discover-description"><span><?= $productName ?></span></div>
            <div class="discover-price"><p>₱<?= $productPrice ?></p></div>
            <div class="shopnow-button"><p>SHOP NOW</p></div>
          </div>
        </a>
      <?php endwhile; ?>
      <?php mysqli_free_result($result); ?>
    </div>
  </div>

  <?php if ($filterType == 'all'): ?>
    <!-- Pagination -->
    <div class="page">
      <?php
        $totalQuery = "SELECT COUNT(*) AS total FROM product WHERE category = '$category'";
        $totalResult = mysqli_query($conn, $totalQuery);
        $totalProducts = mysqli_fetch_assoc($totalResult)['total'];
        $totalPages = ceil($totalProducts / $itemsPerPage);

        for ($i = 1; $i <= $totalPages; $i++):
          $activeClass = ($i == $page) ? 'active-page' : '';
      ?>
        <a href="?filter=<?= $filterType ?>&category=<?= urlencode($category) ?>&page=<?= $i ?>&user=<?= $userName ?>" 
           class="pagination-link <?= $activeClass ?>"><?= $i ?></a>
      <?php endfor; ?>
      <?php mysqli_free_result($totalResult); ?>
    </div>
  <?php endif; ?>

  <?php mysqli_close($conn); ?>
</section>


</body>
</html>
<?php
include ('footer.php');
?>