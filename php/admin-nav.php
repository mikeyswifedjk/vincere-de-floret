<?php
$conn = mysqli_connect("localhost:3306", "root", "", "flowershop");

// Check if the newUsername query parameter is set
if (isset($_GET["newUsername"])) {
    // Retrieve the updated username from the query parameter
    $newAdminName = $_GET["newUsername"];
} else {
    // Check if the username is stored in the session
    if (isset($_SESSION['adminUsername'])) {
        // Retrieve the username from the session
        $newAdminName = $_SESSION['adminUsername'];
    } else {
        // Fetch the actual admin username from the database
        $result = mysqli_query($conn, "SELECT username FROM admin WHERE email = 'admin@gmail.com'");
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $newAdminName = $row['username'];
        } else {
            // If fetching from the database fails, use a default value
            $newAdminName = "ADMIN";
        }
    }
}

// Fetch the profile picture path from the database based on the retrieved username
$result = mysqli_query($conn, "SELECT image FROM admin WHERE username = '$newAdminName'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    $profile_picture = $row['image'];
} else {
    // If fetching from the database fails or no image is found, provide a default path
    $profile_picture = "default_image.jpg"; // Update this with your default image path
}

// Check if the logout form is submitted
if (isset($_POST['logout'])) {
    // Perform logout logic
    session_destroy();
    header("Location: login.php"); // Redirect to the login page after logout
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin-nav.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">
    <title>Admin Page - Sunny Bloom</title>
</head>

<body>
<div class="sidebar">
  <div class="sidebar-content">
    <!-- Admin Profile Button -->
    <a href="admin-account.php?newUsername=<?php echo urlencode($newAdminName); ?>" class="admin-profile">
      <img src="<?php echo $profile_picture; ?>" alt="Admin Profile">
      <span><?php echo $newAdminName; ?></span>
    </a>

    <!-- Navigation Buttons -->
    <nav class="nav-links">
      <a href="admin-dashboard.php" class="nav-btn">
        <i class="fa-solid fa-house-chimney"></i>
        <span>DASHBOARD</span>
      </a>
      <a href="add-product.php" class="nav-btn">
        <i class="fa-solid fa-box-open"></i>
        <span>ADD PRODUCT</span>
      </a>
      <a href="category-management.php" class="nav-btn">
        <i class="fa-solid fa-list"></i>
        <span>CATEGORY</span>
      </a>
      <a href="product-inventory.php" class="nav-btn">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>INVENTORY</span>
      </a>
      <a href="orders.php" class="nav-btn">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>ORDERS</span>
      </a>
      <a href="pos.php" class="nav-btn">
        <i class="fa-solid fa-chart-simple"></i>
        <span>POS</span>
      </a>
      <a href="unlock-user.php" class="nav-btn">
        <i class="fa-solid fa-user-group"></i>
        <span>CUSTOMER</span>
      </a>
      <a href="customer-design-setting.php" class="nav-btn">
        <i class="fa-solid fa-gears"></i>
        <span>DESIGN SETTING</span>
      </a>
      <a href="customer-landing-page.php" class="nav-btn logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>LOG OUT</span>
      </a>
    </nav>
  </div>
</div>

</body>
</html>