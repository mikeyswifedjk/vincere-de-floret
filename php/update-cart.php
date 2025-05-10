<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userName = isset($_POST['user']) ? $_POST['user'] : '';

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $_SESSION = array();
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header("Location: http://localhost/flowershop/php/customer-landing-page.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID
    $userQuery = "SELECT id FROM users WHERE name = ?";
    $userStatement = mysqli_prepare($conn, $userQuery);
    mysqli_stmt_bind_param($userStatement, "s", $userName);
    mysqli_stmt_execute($userStatement);
    $userResult = mysqli_stmt_get_result($userStatement);

    if (!$userResult) {
        die("Error getting user: " . mysqli_error($conn));
    }

    $userRow = mysqli_fetch_assoc($userResult);
    $user_id = isset($userRow['id']) ? $userRow['id'] : 0;

    // Inputs from form
    $cartId = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $newQuantity = isset($_POST['new_quantity']) ? (int)$_POST['new_quantity'] : 1;

    // Get product_id from the cart
    $cartQuery = "SELECT product_id FROM cart WHERE id = ? AND user_id = ?";
    $cartStatement = mysqli_prepare($conn, $cartQuery);
    mysqli_stmt_bind_param($cartStatement, "ii", $cartId, $user_id);
    mysqli_stmt_execute($cartStatement);
    $cartResult = mysqli_stmt_get_result($cartStatement);

    if (!$cartResult || mysqli_num_rows($cartResult) === 0) {
        die("Cart item not found.");
    }

    $cartRow = mysqli_fetch_assoc($cartResult);
    $productId = $cartRow['product_id'];

    // Get latest price from product table
    $productQuery = "SELECT price FROM product WHERE id = ?";
    $productStatement = mysqli_prepare($conn, $productQuery);
    mysqli_stmt_bind_param($productStatement, "i", $productId);
    mysqli_stmt_execute($productStatement);
    $productResult = mysqli_stmt_get_result($productStatement);

    if (!$productResult || mysqli_num_rows($productResult) === 0) {
        die("Product not found.");
    }

    $productRow = mysqli_fetch_assoc($productResult);
    $price = floatval($productRow['price']);
    $totalPrice = $price * $newQuantity;

    // Update cart
    $updateQuery = "UPDATE cart SET quantity = ?, price = ?, total_price = ? WHERE id = ? AND user_id = ?";
    $updateStatement = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStatement, "iddii", $newQuantity, $price, $totalPrice, $cartId, $user_id);
    $updateResult = mysqli_stmt_execute($updateStatement);

    if (!$updateResult) {
        die("Update failed: " . mysqli_error($conn));
    }

    header("Location: cart.php?user=" . urlencode($userName));
    exit;
}

mysqli_close($conn);
?>