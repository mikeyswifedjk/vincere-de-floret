<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

//settings for customer-design-settings
$sqlGetSettings = "SELECT * FROM design_settings WHERE id = 1";
$resultSettings = $conn->query($sqlGetSettings);

if ($resultSettings->num_rows > 0) {
    while ($row = $resultSettings->fetch_assoc()) {
        $bgColor = $row["background_color"];
        $fontColor = $row["font_color"];
        $shopName = $row["shop_name"];
        $logoPath = $row["logo_path"];
        $imageOnePath = $row["image_one_path"];
        $imageTwoPath = $row["image_two_path"];
        $imageThreePath = $row["image_three_path"];
    }
} else {
    echo "0 results";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vincere de Floret</title>

  <link rel="stylesheet" href="../css/customer-dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="icon" type="image/png" href="../assets/logo/logo2.png" />
  <style>
    * {
      color: <?= $fontColor ?>;
    }
    body {
      background-color: <?= $bgColor ?>;
    }
    .auth-link {
      margin-left: 15px;
      font-weight: bold;
      text-decoration: none;
    }
  </style>
</head>
<body>
<header class="header">
  <a href="customer-landing-page.php" class="container-header">
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

  <!-- LOGIN | SIGN UP -->
  <div class="header-right">
    <nav class="nav-right">
      <a href="login.php" class="auth-link">Login</a>
      <a href="register.php" class="auth-link">SignUp</a>
    </nav>
  </div>
</header>

<!-- Image Slider -->
<section class="image-slider container-imageSlider">
  <?php foreach ([$imageOnePath, $imageTwoPath, $imageThreePath] as $index => $imgPath): ?>
    <div class="mySlides fade">
      <img class="slider-image" src="../img/<?= basename($imgPath) ?>" alt="Slide <?= $index + 1 ?>" />
    </div>
  <?php endforeach; ?>
  <div class="DOT" style="text-align: center">
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>
</section>

<!-- Categories Section -->
<section class="content-categories">
  <div class="categories-title"><p>Categories</p></div>
  <div class="containers-category">
    <?php
      $categoryQuery = "SELECT * FROM category";
      $categoryResult = mysqli_query($conn, $categoryQuery);
      while ($row = mysqli_fetch_assoc($categoryResult)):
        $categoryName = htmlspecialchars($row['category']);
    ?>
      <a class="category-link" href="login.php" onclick="alert('Please log in to view this category.')">
        <div class="categories">
          <div class="category-title"><span><?= $categoryName ?></span></div>
        </div>
      </a>
    <?php endwhile; ?>
  </div>
</section>

<!-- Best Selling Items -->
<section class="daily-discover-content">
  <div class="daily-discover-title"><h3>Best Selling Items</h3></div>
  <div class="daily-discover-container">
    <div class="grid-items">
      <?php
        $bestSellerQuery = "
          SELECT p.id, p.name, p.image, p.price, SUM(oi.quantity) as total_sold
          FROM order_items oi
          JOIN product p ON oi.product_name = p.name
          GROUP BY p.id
          HAVING total_sold > 3
          ORDER BY total_sold DESC
          LIMIT 6
        ";
        $bestSellerResult = mysqli_query($conn, $bestSellerQuery);
        while ($row = mysqli_fetch_assoc($bestSellerResult)):
      ?>
        <a class="product-link" href="login.php" onclick="alert('Please log in to view product details.')">
          <div class="items">
            <img src="../img/<?= $row['image'] ?>" alt="<?= $row['name'] ?>" />
            <div class="discover-description"><span><?= $row['name'] ?></span></div>
            <div class="discover-price"><p>₱<?= number_format($row['price'], 2) ?></p></div>
            <div class="shopnow-button"><p>Shop Now</p></div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- Product Grid -->
<section class="daily-discover-content" id="product">
  <div class="daily-discover-title"><h3>Package Bundle</h3></div>
  <div class="daily-discover-container">
    <div class="grid-items">
      <?php
        $itemsPerPage = 18;
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $itemsPerPage;

        $productQuery = "SELECT * FROM product ORDER BY RAND() LIMIT $offset, $itemsPerPage";
        $productResult = mysqli_query($conn, $productQuery);

        while ($product = mysqli_fetch_assoc($productResult)):
      ?>
        <a class="product-link" href="login.php" onclick="alert('Please log in to view product details.')">
          <div class="items">
            <img src="../img/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
            <div class="discover-description"><span><?= $product['name'] ?></span></div>
            <div class="discover-price"><p>₱<?= number_format($product['price'], 2) ?></p></div>
            <div class="shopnow-button"><p>Shop Now</p></div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Pagination -->
  <div class="page">
    <?php
      $totalQuery = "SELECT COUNT(*) AS total FROM product";
      $totalResult = mysqli_query($conn, $totalQuery);
      $total = mysqli_fetch_assoc($totalResult)['total'];
      $totalPages = ceil($total / $itemsPerPage);

      for ($i = 1; $i <= $totalPages; $i++):
        $activeClass = ($i == $page) ? 'active-page' : '';
    ?>
      <a href="?page=<?= $i ?>" class="pagination-link <?= $activeClass ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php mysqli_close($conn); ?>
  </div>
</section>

<!-- Slider Script -->
<script>
  let slideIndex = 0;
  function showSlides() {
    const slides = document.getElementsByClassName("mySlides");
    const dots = document.getElementsByClassName("dot");
    for (let slide of slides) slide.style.display = "none";
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;
    for (let dot of dots) dot.classList.remove("active");
    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].classList.add("active");
    setTimeout(showSlides, 2000);
  }
  showSlides();
</script>

</body>
</html>
<?php include 'footer.php'; ?>