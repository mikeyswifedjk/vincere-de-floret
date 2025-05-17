<?php
include 'admin-change-password.php'; 
include('admin-nav.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/admin-password-settings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">
    <title> Password Settings - Vincere De Floret</title>
</head>
<body>
    <div class="main-container">
        <div class="all">
            <div class="add">
                <h1 class="text1">Password Settings</h1>
                <form method="post">
                    <!-- Current Password -->
                    <label for="current_password">Old Password</label> 
                    <input type="password" id="current_password" name="current_password" placeholder="Enter your old password" required />

                    <label for="password">New Password</label> 
                    <input type="password" id="password" name="password" placeholder="Enter your new password" value="" required />

                    <!-- Confirm New Password -->
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" value="" required />

                    <!-- Submit Button -->
                    <button class="change buttonProduct" type="submit" name="change_password">Change Password</button>
                    </form>
            </div>
        </div>
    </div>
</body>
</html>