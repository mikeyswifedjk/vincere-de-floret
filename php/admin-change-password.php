<?php
//admin password function
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // Database connection (modify the credentials as needed)
    $dbConnection = mysqli_connect("localhost:3306", "root", "", "vincere_de_floret");

    // Check connection
    if (!$dbConnection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate and update the new password
    $currentPassword = isset($_POST['current_password']) ? mysqli_real_escape_string($dbConnection, $_POST['current_password']) : "";
    $newPassword = isset($_POST['password']) ? mysqli_real_escape_string($dbConnection, $_POST['password']) : "";
    $confirmPassword = isset($_POST['confirm_password']) ? mysqli_real_escape_string($dbConnection, $_POST['confirm_password']) : "";

    // Retrieve the current hashed password from the database
    $queryPassword = "SELECT password FROM admin WHERE email='admin@gmail.com'";
    $resultPassword = mysqli_query($dbConnection, $queryPassword);

    if ($resultPassword && mysqli_num_rows($resultPassword) > 0) {
        $rowPassword = mysqli_fetch_assoc($resultPassword);
        $currentPasswordHash = $rowPassword['password'];

        // Check if the current password matches the one in the database
        if (password_verify($currentPassword, $currentPasswordHash)) {
            // Check if the new password is different from the old one
            if ($currentPassword != $newPassword) {
                // Continue with the password change logic
                if (!empty($newPassword) && !empty($confirmPassword) && $newPassword === $confirmPassword) {
                    // Update the password in the database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE admin SET password='$hashedPassword' WHERE email='admin@gmail.com'";
                    $updateResult = mysqli_query($dbConnection, $updateQuery);

                    if ($updateResult) {
                        echo "<script>alert('Password Update Successfully!');</script>";
                    } else {
                        // Error updating password
                        echo "Error updating password: " . mysqli_error($dbConnection);
                    }
                } else {
                    echo "<script>alert('Password and Confirm Password doesn\'t match!');</script>";
                }
            } else {
                echo "<script>alert('New password cannot be the same as the old password!');</script>";
            }
        } else {
            echo "<script>alert('Current Password is incorrect!');</script>";
        }
    } else {
        // Handle the case where the password is not found
        echo "Error retrieving the current password.";
    }

    // Close the database connection
    mysqli_close($dbConnection);
}
?>