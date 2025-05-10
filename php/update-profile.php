<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include("user-profile-settings.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input fields
    $newUsername = htmlspecialchars($_POST["new_username"]);
    $address = htmlspecialchars($_POST["address"]);
    $contactNumber = htmlspecialchars($_POST["contact_number"]);
    $firstName = htmlspecialchars($_POST["first_name"]);
    $middleName = htmlspecialchars($_POST["middle_name"]);
    $lastName = htmlspecialchars($_POST["last_name"]);

    // Initialize $targetFile variable
    $targetFile = null;

    // Handle file upload only if a new profile picture is selected
    if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {
        $targetDir = "../img/"; // Specify your target directory
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $targetFile = $targetDir . uniqid() . '.' . $imageFileType;

        // Check if the file is an actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (adjust as needed)
        if ($_FILES["profile_picture"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                echo "<script>alert('The file " . basename($_FILES["profile_picture"]["name"]) . " has been uploaded.');</script>";

                // Update the session with the new image path
                $_SESSION['image_path'] = $targetFile;

                // Call the JavaScript function to update the profile image
                echo '<script type="text/javascript">updateProfileImage("' . $_SESSION['image_path'] . '?' . time() . '");</script>';
            }
        }
    }

    // Set the image path in the database to the target file path
    $imagePath = $targetFile;

    // Update user information in the database
    $updateQuery = "UPDATE users SET name=?, address=?, contact_number=?, first_name=?, middle_name=?, last_name=? " . ($targetFile ? ", image_path=?" : "") . " WHERE name=?";

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $updateQuery);

    if ($stmt) {
        // Bind parameters dynamically based on whether $targetFile is set
        if ($targetFile) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $newUsername, $address, $contactNumber, $firstName, $middleName, $lastName, $imagePath, $_SESSION['user_name']);
        } else {
            mysqli_stmt_bind_param($stmt, "sssssss", $newUsername, $address, $contactNumber, $firstName, $middleName, $lastName, $_SESSION['user_name']);
        }

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Update the session with the new username
            $_SESSION['user_name'] = $newUsername;

            // After updating the session with new username and image path
            unset($_SESSION['first_name']);
            unset($_SESSION['middle_name']);
            unset($_SESSION['last_name']);
            unset($_SESSION['address']);
            unset($_SESSION['contact_number']);

            // Close the prepared statement
            mysqli_stmt_close($stmt);

            // Close the database connection
            mysqli_close($conn);

            echo "Profile updated successfully!";
            // Add this code after the successful update in update-profile.php
            echo '<script>window.location.href = "user-profile-settings.php";</script>';
        } else {
            echo "Error updating profile: " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    // Redirect to an error page if accessed directly without form submission
    header("Location: error-page.php");
    exit;
}
?>