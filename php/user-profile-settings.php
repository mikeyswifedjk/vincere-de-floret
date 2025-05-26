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
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/vincere-de-floret/php/customer-landing-page.php");
    exit;
}

// Retrieve user information from the database
$query = "SELECT image_path, address, first_name, middle_name, last_name, contact_number FROM users WHERE name='$userName'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['image_path'] = $row['image_path'];
    $_SESSION['address'] = $row['address'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['middle_name'] = $row['middle_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['contact_number'] = $row['contact_number'];
}

// Fetch email and verification status
$emailCheckQuery = "SELECT email, email_verified_at FROM users WHERE name = ?";
$emailStmt = $conn->prepare($emailCheckQuery);
$emailStmt->bind_param("s", $userName);
$emailStmt->execute();
$emailResult = $emailStmt->get_result();
$userData = $emailResult->fetch_assoc();

$isVerified = !empty($userData['email_verified_at']);
$email = $userData['email'] ?? '';

// Settings for customer-design-settings
$sqlGetSettings = "SELECT * FROM design_settings WHERE id = 1";
$resultSettings = $conn->query($sqlGetSettings);

if ($resultSettings->num_rows > 0) {
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
  <link rel="stylesheet" href="../css/user-profile-settings.css">
  <title>Account Settings</title>
  <style>
    * {
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

  <div class="content-search">
    <input type="text" class="search-bar" placeholder="Search products..." />
    <button class="search-button">
      <i class="fa-solid fa-magnifying-glass"></i>
    </button>
  </div>

  <div class="header-right">
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

<div>
  <form action="update-profile.php" class="settings-grid" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
    <!-- LEFT SIDE -->
    <div class="settings-left">
      <div class="profile-box">
        <img id="profileImage" class="profile-image"
          src="<?php echo isset($_SESSION['image_path']) ? $_SESSION['image_path'] . '?' . time() : ''; ?>"
          alt="Profile Picture">
        <label for="profile_picture" class="upload-btn">Change Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" hidden>
      </div>

      <div class="verification-box">
        <?php if (!$isVerified): ?>
          <div class="verify-warning">
            <p>Your account is not verified. Check your email: <strong><?= htmlspecialchars($email) ?></strong></p>
            <form action="resend-verification.php" method="post">
              <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
              <button type="submit" class="resend-btn">Resend Verification</button>
            </form>
          </div>
        <?php else: ?>
          <div class="verify-success">
            <span class="checkmark">✓</span> Email Verified
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="settings-right">
      <h1>ACCOUNT SETTINGS</h1>
      <p>Manage and protect your account</p>

      <div class="form-group">
        <label for="new_username">Username:</label>
        <input type="text" id="new_username" name="new_username"
               value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name"
               value="<?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name"
               value="<?php echo htmlspecialchars($_SESSION['middle_name'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name"
               value="<?php echo htmlspecialchars($_SESSION['last_name'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address"
               value="<?php echo htmlspecialchars($_SESSION['address'] ?? ''); ?>" required>
      </div>

      <div class="form-group">
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" pattern="[0-9]{11}"
               value="<?php echo htmlspecialchars($_SESSION['contact_number'] ?? ''); ?>" required>
      </div>

      <button type="submit" class="save-btn">Save Changes</button>
    </div>
  </form>
</div>

<script>
  function updateProfileImage(newImagePath) {
    document.getElementById('profileImage').src = newImagePath;
  }

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
<?php include('footer.php'); ?>
