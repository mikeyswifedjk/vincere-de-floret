<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php');
require 'connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check kung may existing record na
$sqlCheck = "SELECT * FROM design_settings WHERE id = 1";
$resultCheck = $conn->query($sqlCheck);

if ($resultCheck->num_rows == 0) {
    $sqlInsert = "INSERT INTO design_settings (background_color, font_color, shop_name, logo_path, image_one_path, image_two_path, image_three_path)
    VALUES ('#fff8f0', '#333', 'My Shop', 'default_logo.png', 'default_image1.png', 'default_image2.png', 'default_image3.png')";

    if ($conn->query($sqlInsert) === TRUE) {
        echo "Default record added successfully";
    } else {
        echo "Error adding default record: " . $conn->error;
    }
}

// Kumuha ng design settings mula sa database
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

function isValidAdmin($inputPassword) {
    global $conn;

    $sql = "SELECT password FROM admin WHERE email = 'admin@gmail.com'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        // Compare input with hashed password
        return password_verify($inputPassword, $hashedPassword);
    }

    return false;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminPassword = $_POST["admin_password"] ?? '';

    if (!isValidAdmin($adminPassword)) {
        echo "<script>alert('Invalid admin password. Update blocked.'); window.location.href = window.location.href;</script>";
        exit();
    
    } elseif (isset($_POST["updateColors"])) {
        // Handle color form submission
        $bgColor = $_POST["background_color"];
        $fontColor = $_POST["font_color"];

        $sqlUpdateColors = "UPDATE design_settings SET
            background_color='$bgColor',
            font_color='$fontColor'
        WHERE id = 1";

        if ($conn->query($sqlUpdateColors) === TRUE) {
            // Refresh the page after successful update
            echo "<script>
                    alert('Updated Successfully');
                    window.location.href = window.location.href; // Refresh the page
                  </script>";
        } else {
            echo "Error updating colors: " . $conn->error;
        }
    } elseif (isset($_POST["clearColors"])) {
        // Clear All Colors
        $bgColor = '#FFF8F0';
        $fontColor = '#333';

        $sqlClearColors = "UPDATE design_settings SET
            background_color='$bgColor',
            font_color='$fontColor'
        WHERE id = 1";

        if ($conn->query($sqlClearColors) === TRUE) {
            // Refresh the page after clearing colors
            echo "<script>
                    alert('Updated Successfully');
                    window.location.href = window.location.href; // Refresh the page
                  </script>";
        } else {
            echo "Error clearing colors: " . $conn->error;
        }
    } elseif (isset($_POST["updateShopDetails"])) {
        $shopName = $_POST["shop_name"];
    
        // Keep current logo path
        $currentLogoPath = $logoPath;
    
        if ($_FILES["logo_path"]["size"] > 0) {
            $targetDirectory = "../img/";
            $logoPath = $targetDirectory . basename($_FILES["logo_path"]["name"]);
            move_uploaded_file($_FILES["logo_path"]["tmp_name"], $logoPath);
    
            $sqlUpdateShopDetails = "UPDATE design_settings SET
                shop_name='$shopName',
                logo_path='$logoPath'
            WHERE id = 1";
        } else {
            $sqlUpdateShopDetails = "UPDATE design_settings SET
                shop_name='$shopName',
                logo_path='$currentLogoPath'
            WHERE id = 1";
        }
    
        if ($conn->query($sqlUpdateShopDetails) === TRUE) {
            echo "<script>alert('Updated Successfully'); window.location.href = window.location.href;</script>";
        } else {
            echo "Error updating shop details: " . $conn->error;
        }    
    } elseif (isset($_POST["updateImages"])) {
        // Handle image uploads with fallback
        handleImageUpload($_FILES["image_one_path"], "image_one_path", $imageOnePath);
        handleImageUpload($_FILES["image_two_path"], "image_two_path", $imageTwoPath);
        handleImageUpload($_FILES["image_three_path"], "image_three_path", $imageThreePath);
    
        // Refresh after all image updates
        echo "<script>alert('Images updated successfully'); window.location.href = window.location.href;</script>";
    }       
}

$conn->close();

function handleImageUpload($file, $column, $existingPath) {
    global $conn;

    $targetDirectory = "../img/";
    $newPath = $existingPath;

    if ($file["size"] > 0) {
        $newPath = $targetDirectory . basename($file["name"]);
        move_uploaded_file($file["tmp_name"], $newPath);
    }

    $sqlUpdateFile = "UPDATE design_settings SET
        $column='$newPath'
    WHERE id = 1";

    if ($conn->query($sqlUpdateFile) === TRUE) {
        echo "<script>console.log('$column updated successfully');</script>";
    } else {
        echo "Error updating " . $column . ": " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/customer-design-setting.css">
    <title>Design Setting - Sunny Bloom</title>
    <style>
        body {
            background-color: <?php echo $bgColor; ?>;
            color: <?php echo $fontColor; ?>;
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="content-wrapper">

    <div class="all">

    <h1 class="settings-title">Customer Design Settings</h1>

<div class="settings-panel">

    <!-- Color Settings -->
    <section class="settings-section color-settings">
        <h2 class="section-title">Color Settings</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="colorForm" enctype="multipart/form-data">
            <label for="background_color">Background Color:</label>
            <input type="color" id="background_color" name="background_color" value="<?php echo $bgColor; ?>" required />

            <label for="font_color">Font Color:</label>
            <input type="color" id="font_color" name="font_color" value="<?php echo $fontColor; ?>" required />

            <div class="button-group">
                <button type="submit" name="updateColors">Save</button>
                <button type="button" class="resetBtn" onclick="clearColors()">Reset</button>
            </div>
        </form>
    </section>

    <!-- Logo & Shop Name -->
    <section class="settings-section shop-settings">
        <h2 class="section-title">Logo & Shop Name</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="shopDetailsForm" enctype="multipart/form-data">
            <label for="shop_name">Shop Name:</label>
            <input type="text" id="shop_name" name="shop_name" value="<?php echo $shopName; ?>" required />

            <label for="logo_path">Logo:</label>
            <input type="file" id="logo_path" name="logo_path" />

            <div class="preview-container">
                <img src="../img/<?php echo basename($logoPath); ?>" alt="Logo Preview">
                <p class="note">File Size: Max 70kb | Extensions: .jpg, .jpeg, .png</p>
            </div>

            <button type="submit" name="updateShopDetails">Update Details</button>
        </form>
    </section>

    <!-- Image Slider -->
    <section class="settings-section slider-settings">
        <h2 class="section-title">Image Slider</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="imagesForm" enctype="multipart/form-data">
            <div class="image-upload">
                <label for="image_one_path">Image One:</label>
                <input type="file" id="image_one_path" name="image_one_path" />
                <div class="preview-container">
                    <img src="../img/<?php echo basename($imageOnePath); ?>" alt="Image One Preview">
                    <p class="note">File Size: Max 70kb | Extensions: .jpg, .jpeg, .png</p>
                </div>
            </div>

            <div class="image-upload">
                <label for="image_two_path">Image Two:</label>
                <input type="file" id="image_two_path" name="image_two_path" />
                <div class="preview-container">
                    <img src="../img/<?php echo basename($imageTwoPath); ?>" alt="Image Two Preview">
                    <p class="note">File Size: Max 70kb | Extensions: .jpg, .jpeg, .png</p>
                </div>
            </div>

            <div class="image-upload">
                <label for="image_three_path">Image Three:</label>
                <input type="file" id="image_three_path" name="image_three_path" />
                <div class="preview-container">
                    <img src="../img/<?php echo basename($imageThreePath); ?>" alt="Image Three Preview">
                    <p class="note">File Size: Max 70kb | Extensions: .jpg, .jpeg, .png</p>
                </div>
            </div>

            <button type="submit" name="updateImages">Update Slider</button>
        </form>
    </section>

    <!-- Admin Authentication -->
    <section class="settings-section auth-section">
        <h2 class="section-title">Admin Authentication</h2>
        <form id="authForm">
            <label for="admin_password">Enter Admin Password:</label>
            <input type="password" id="admin_password" name="admin_password" required />
            <p class="note">Required for saving or updating any settings.</p>
        </form>
    </section>

</div>
    </div>
    </div>
    </div>
    </div>
    </div>

    <script>

        // Inject password from auth tab into all forms
        const adminPasswordInput = document.getElementById('admin_password');
        const allForms = document.querySelectorAll('form');

        allForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const password = adminPasswordInput.value;
                if (!password) {
                    e.preventDefault();
                    alert("Please enter admin password.");
                    return;
                }

                // Add password to form as hidden input
                if (!form.querySelector('input[name="admin_password"]')) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'admin_password';
                    hidden.value = password;
                    form.appendChild(hidden);
                }
            });
        });

        function clearColors() {
            // Set default values for background and font colors
            document.getElementById('background_color').value = '#fff8f0';
            document.getElementById('font_color').value = '#333';
        }
    </script>
</body>
</html>