<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['order_details'])) {
    header("Location: checkout.php");
    exit;
}

$orderDetails = $_SESSION['order_details'];
$error = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gcashNumber = $_POST['gcash_number'];
    $gcashAmount = $_POST['gcash_amount'];

    if ($gcashAmount == $orderDetails['total_amount']) {
        header("Location: order-confirmation.php");
        exit;
    } else {
        $error = "The payment amount must be exactly ₱" . number_format($orderDetails['total_amount'], 2);
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
    <title>Gcash Payment</title>
    <link rel="stylesheet" type="text/css" href="../css/gcash-payment.css">
</head>
<body>
<div class="background-image">
  <img src="../assets/gcash.jpg" alt="GCash Background" />
</div>

<div class="payment-container">
  <h1 class="gcash-title">GCash Payment</h1>
  <p class="instruction">Please complete your payment using GCash.</p>
  <p class="amount">Total Amount: <span>₱<?= number_format($orderDetails['total_amount'], 2) ?></span></p>

  <?php if (isset($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="gcash-payment.php" method="POST" class="payment-form">
    <label for="gcash_number">GCash Number:</label>
    <input type="text" name="gcash_number" id="gcash_number" placeholder="09xx-xxx-xxxx" required>

    <label for="gcash_amount">Amount:</label>
    <input type="number" step="0.01" name="gcash_amount" id="gcash_amount" required>

    <button type="submit">Confirm Payment</button>
  </form>
</div>

</body>
</html>
