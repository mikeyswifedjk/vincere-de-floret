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

// Settings for customer-design-settings
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/user-profile-settings.css">
    <title>Account Settings</title>
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

<!-- Profile Settings Section -->
<div class="settings">
  <h1>ACCOUNT SETTINGS</h1>
  <p>Manage and protect your account</p>

  <?php
  $emailCheckQuery = "SELECT email, email_verified_at FROM users WHERE name = ?";
  $emailStmt = $conn->prepare($emailCheckQuery);
  $emailStmt->bind_param("s", $userName);
  $emailStmt->execute();
  $emailResult = $emailStmt->get_result();
  $userData = $emailResult->fetch_assoc();

  $isVerified = !empty($userData['email_verified_at']);
  $email = $userData['email'];
  ?>

  <div class="verification-box" style="padding: 15px; margin: 20px 0; text-align: center;">
    <?php if (!$isVerified): ?>
      <div style="background: #ffe9e9; padding: 15px; border: 1px solid #ff5c5c; border-radius: 8px;">
        <p style="color: #cc0000; font-weight: bold;">Your account is not verified. Please check your email: <strong><?= htmlspecialchars($email) ?></strong></p>
        <form action="resend-verification.php" method="post" style="margin-top: 10px;">
          <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
          <button type="submit" style="background: #cc0000; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Resend Verification Email</button>
        </form>
      </div>
    <?php else: ?>
      <div style="display: inline-flex; align-items: center; gap: 10px; color: green; font-weight: bold;">
        <div style="width: 24px; height: 24px; background-color: green; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">
          ✓
        </div>
        Email Verified
      </div>
    <?php endif; ?>
  </div>

  <form action="update-profile.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
    <!-- Profile Picture -->
    <div class="form-group">
      <div id="profilePicturePreviewContainer"></div>
      <img
        id="profileImage"
        class="profile-image"
        src="<?php echo isset($_SESSION['image_path']) ? $_SESSION['image_path'] . '?' . time() : ''; ?>"
        alt="Profile Picture"
      >
      <label for="profile_picture">Profile Picture</label>
      <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
    </div>

    <!-- Username -->
    <div class="form-group">
      <label for="new_username">Username:</label>
      <input
        type="text"
        id="new_username"
        name="new_username"
        value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>"
        required
      />
    </div>

    <!-- First Name -->
    <div class="form-group">
      <label for="first_name">First Name:</label>
      <input
        type="text"
        id="first_name"
        name="first_name"
        placeholder="Enter your first name"
        value="<?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>"
        required
      />
    </div>

    <!-- Middle Name -->
    <div class="form-group">
      <label for="middle_name">Middle Name:</label>
      <input
        type="text"
        id="middle_name"
        name="middle_name"
        placeholder="Enter your middle name"
        value="<?php echo isset($_SESSION['middle_name']) ? htmlspecialchars($_SESSION['middle_name']) : ''; ?>"
        required
      />
    </div>

    <!-- Last Name -->
    <div class="form-group">
      <label for="last_name">Last Name:</label>
      <input
        type="text"
        id="last_name"
        name="last_name"
        placeholder="Enter your last name"
        value="<?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : ''; ?>"
        required
      />
    </div>

    <!-- Address -->
    <div class="form-group">
      <label for="address">Address:</label>
      <input
        type="text"
        id="address"
        name="address"
        placeholder="Enter your address"
        value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>"
        required
      />
    </div>

    <!-- Contact Number -->
    <div class="form-group">
      <label for="contact_number">Contact Number:</label>
      <input
        type="text"
        id="contact_number"
        name="contact_number"
        pattern="[0-9]{11}"
        placeholder="Enter your contact number"
        value="<?php echo $_SESSION['contact_number']; ?>"
        required
      />
    </div>

    <!-- Submit Button -->
    <button type="submit" class="save-btn">Save Changes</button>
  </form>
</div>

<script>
    // Updates the profile image preview
    function updateProfileImage(newImagePath) {
        document.getElementById('profileImage').src = newImagePath;
    }

    // Validates the contact number format
    function validateForm() {
        var contactNumber = document.getElementById("contact_number").value;
        var pattern = /^[0-9]{11}$/;
        if (!pattern.test(contactNumber)) {
            alert("Please enter a valid 11-digit contact number.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
<?php
include ('footer.php');
?>