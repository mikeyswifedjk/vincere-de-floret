<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('change-password.php');
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
        header("Location: http://localhost/flowershop/php/customer-landing-page.php");
        exit;
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

        // Retrieve user information from the database
        $query = "SELECT image_path, address, first_name, middle_name, last_name, contact_number FROM users WHERE name='$userName'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            // Save the user information in session variables
            $_SESSION['image_path'] = $row['image_path'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['middle_name'] = $row['middle_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['contact_number'] = $row['contact_number'];    
            
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/user-change-password.css">
    <title>Password Settings</title>
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
    
<!-- Password Settings Section -->
<div class="settings">
  <h1>PASSWORD SETTINGS</h1>
  <p>Manage password and security</p>

  <form method="post">
    <!-- Current Password -->
    <div class="form-group">
      <label for="current_password">Current Password:</label>
      <input
        type="password"
        id="current_password"
        name="current_password"
        placeholder="Enter your old password"
        required
      />
    </div>

    <!-- New Password -->
    <div class="form-group">
      <label for="password">New Password:</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter your new password"
        required
      />
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
      <label for="confirm_password">Confirm New Password:</label>
      <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        placeholder="Re-enter your new password"
        required
      />
    </div>

    <!-- Submit Button -->
    <button type="submit" name="change_password">Change Password</button>
  </form>
</div>

<script>
  // Optional utility for image update (currently unused here)
  function updateProfileImage(newImagePath) {
    document.getElementById('profileImage').src = newImagePath;
  }
</script>

</body>
</html>
<?php
include ('footer.php');
?>