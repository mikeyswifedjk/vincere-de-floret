<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('admin-nav.php');

// Add New Account
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_account"])) {
    $name = mysqli_real_escape_string($conn, $_POST["name"] ?? '');
    $firstName = mysqli_real_escape_string($conn, $_POST["first_name"] ?? '');
    $middleName = mysqli_real_escape_string($conn, $_POST["middle_name"] ?? '');
    $lastName = mysqli_real_escape_string($conn, $_POST["last_name"] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST["new_email"] ?? '');
    $contactNumber = mysqli_real_escape_string($conn, $_POST["contact_number"] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST["address"] ?? '');

    $defaultPassword = '0000';
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
    $verificationCode = rand(100000, 999999);

    $insertSql = "INSERT INTO users (name, first_name, middle_name, last_name, email, password, verification_code, blocked, attempts, contact_number, address) 
                  VALUES ('$name', '$firstName', '$middleName', '$lastName', '$email', '$hashedPassword', '$verificationCode', 0, 0, '$contactNumber', '$address')";

    if (mysqli_query($conn, $insertSql)) {
        echo "<script>alert('New account added successfully! Default password is set to 0000.');</script>";
    } else {
        echo "<script>alert('Failed to add account. Please try again.');</script>";
    }
}

// Block/Unblock Functions
function blockUser($conn, $email) {
    return mysqli_query($conn, "UPDATE users SET blocked = 1 WHERE email = '$email'");
}

function unBlock($conn, $email) {
    return mysqli_query($conn, "UPDATE users SET blocked = 0, attempts = 0 WHERE email = '$email'");
}

// Handle Search
$searchEmail = $_GET["searchEmail"] ?? '';
$sql = $searchEmail ? 
    "SELECT * FROM users WHERE email LIKE '%" . mysqli_real_escape_string($conn, $searchEmail) . "%'" :
    "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

// Handle Post Actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle Deletion
    if (isset($_POST["delete_selected"]) && isset($_POST["selectedEmails"])) {
        foreach ($_POST["selectedEmails"] as $email) {
            $safeEmail = mysqli_real_escape_string($conn, $email);
            mysqli_query($conn, "DELETE FROM users WHERE email = '$safeEmail'");
        }
        echo "<script>alert('Selected account(s) deleted successfully.'); document.location.href = 'unlock-user.php';</script>";
        exit;
    }

    // Handle Block/Unblock
    if (isset($_POST["block_user"]) || isset($_POST["unblock_user"])) {
        $adminPass = $_POST["admin_password"] ?? '';
        if (!$adminPass) {
            echo "<script>alert('Missing admin password.'); document.location.href = 'unlock-user.php';</script>";
            exit;
        }

        $check = mysqli_query($conn, "SELECT password FROM admin LIMIT 1");
        $row = mysqli_fetch_assoc($check);
        if (!password_verify($adminPass, $row['password'])) {
            echo "<script>alert('Invalid admin password.'); document.location.href = 'unlock-user.php';</script>";
            exit;
        }

        $email = mysqli_real_escape_string($conn, $_POST["email"]);

        if (isset($_POST["block_user"])) {
            $success = blockUser($conn, $email);
            echo "<script>alert('User with email $email has been " . ($success ? "blocked" : "not blocked") . " successfully.'); document.location.href = 'unlock-user.php';</script>";
        }

        if (isset($_POST["unblock_user"])) {
            $success = unBlock($conn, $email);
            echo "<script>alert('User with email $email has been " . ($success ? "unblocked" : "not unblocked") . " successfully.'); document.location.href = 'unlock-user.php';</script>";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/unlock-user.css">
</head>
<body>
<div class="main-container">
    <div class="content-wrapper">

    <div class="all">
        <h1 class="text1">Account Access Management</h1>
        <button class="report-btn" onclick="generateReport()">Generate Report</button>

        <!-- Add New Account Form -->
<div class="add">
    <h2 class="text4">Add New Account</h2>
    <form method="post" action="unlock-user.php">
        <label for="name">Username:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="new_email">Email:</label>
        <input type="email" id="new_email" name="new_email" required><br><br>

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" required><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="0000" readonly><br><br>

        <button type="submit" class="buttonProduct" name="add_account">Add Account</button>
    </form>
</div>

    <div class="view">
    <h2 class="text4">Customers List</h2>

    <div class="table-controls">
    <!-- Search Form -->
    <form method="get" action="unlock-user.php" class="search-form">
        <input type="email" name="searchEmail" placeholder="Enter email address" required>
        <button type="submit" class="btnSearch">Search</button>
    </form>
    </div>

    <!-- Account Table and Bulk Delete -->
    <form method="POST">
    <button type="submit" name="delete_selected" class="deletebtn">Delete Selected</button>
        <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
            <tr>
                <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                <th>Username</th>
                <th>Email</th>
                <th>Attempts</th>
                <th>Blocked</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_array($result)): ?>
                <tr>
                    <td><input type="checkbox" name="selectedEmails[]" value="<?= htmlspecialchars($row['email']) ?>"></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= (int)$row['attempts'] ?></td>
                    <td><?= (int)$row['blocked'] ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirmAdminPassword(this);" style="display:inline;">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                            <input type="hidden" name="admin_password">
                            <button type="submit" name="<?= $row['blocked'] == 0 ? 'block_user' : 'unblock_user' ?>" class="<?= $row['blocked'] == 0 ? 'blockbtn' : 'unblockbtn' ?>">
                                <?= $row['blocked'] == 0 ? 'Block' : 'Unblock' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </form>
</div>

<script>
    function generateReport() {
        window.location.href = 'users-report.php';
    }

    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('input[name="selectedEmails[]"]');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    function confirmAdminPassword(form) {
        const password = prompt("Enter Admin Password:");
        if (password) {
            form.querySelector('input[name="admin_password"]').value = password;
            return true;
        } else {
            alert("Action cancelled.");
            return false;
        }
    }
</script>

</body>
</html>
