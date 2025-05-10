<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userName = isset($_GET['user']) ? $_GET['user'] : '';

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $_SESSION = array();
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header("Location: http://localhost/flowershop/php/customer-landing-page.php");
    exit;
}

// Fetch product information from the URL parameters
$productId = isset($_GET['id']) ? $_GET['id'] : 0;
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;

// Get the user_id based on the user_name
$userQuery = "SELECT id FROM users WHERE name = ?";
$userStatement = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($userStatement, "s", $userName);
mysqli_stmt_execute($userStatement);
$userResult = mysqli_stmt_get_result($userStatement);

if (!$userResult) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$userRow = mysqli_fetch_assoc($userResult);
$user_id = isset($userRow['id']) ? $userRow['id'] : 0;

// Get product image and price from product table
$productInfoQuery = "SELECT image, price FROM product WHERE id = ?";
$productInfoStatement = mysqli_prepare($conn, $productInfoQuery);
mysqli_stmt_bind_param($productInfoStatement, "i", $productId);
mysqli_stmt_execute($productInfoStatement);
$productInfoResult = mysqli_stmt_get_result($productInfoStatement);

if (!$productInfoResult) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$productInfoRow = mysqli_fetch_assoc($productInfoResult);
$productImage = isset($productInfoRow['image']) ? $productInfoRow['image'] : '';
$price = isset($productInfoRow['price']) ? $productInfoRow['price'] : 0.0;

// Check if the product is already in the cart
$cartQuery = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$cartStatement = mysqli_prepare($conn, $cartQuery);
mysqli_stmt_bind_param($cartStatement, "ii", $user_id, $productId);
mysqli_stmt_execute($cartStatement);
$cartResult = mysqli_stmt_get_result($cartStatement);

if (!$cartResult) {
    die("Error in SQL query: " . mysqli_error($conn));
}

if (mysqli_num_rows($cartResult) == 0) {
    // Insert new product into cart
    mysqli_begin_transaction($conn);
    $insertQuery = "INSERT INTO cart (user_id, product_id, product_name, quantity, product_image, price) VALUES (?, ?, ?, ?, ?, ?)";
    $insertStatement = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($insertStatement, "iisssd", $user_id, $productId, $productName, $quantity, $productImage, $price);
    $insertResult = mysqli_stmt_execute($insertStatement);

    if ($insertResult) {
        mysqli_commit($conn);
        echo '<script>alert("Product added to cart successfully!"); window.history.back();</script>';
    } else {
        mysqli_rollback($conn);
        die("Error inserting to cart: " . mysqli_error($conn));
    }
} else {
    // Product already in cart, update quantity
    $updateQuery = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
    $updateStatement = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStatement, "iii", $quantity, $user_id, $productId);
    $updateResult = mysqli_stmt_execute($updateStatement);

    if ($updateResult) {
        echo '<script>alert("Product quantity updated!"); window.history.back();</script>';
    } else {
        die("Error updating cart: " . mysqli_error($conn));
    }
}

mysqli_close($conn);
?>