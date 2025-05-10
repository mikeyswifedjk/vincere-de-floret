<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['order_details'])) {
    header("Location: checkout.php");
    exit;
}

$orderDetails = $_SESSION['order_details'];

// Assuming COD doesn't require any form submission, proceed directly to order confirmation
header("Location: order-confirmation.php");
exit;
?>