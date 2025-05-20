<?php
// Database Connection
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php'); 

$dbConnection = mysqli_connect("localhost:3306", "root", "", "vincere_de_floret");

if (!$dbConnection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to generate random discount code
function generateRandomCode() {
    $letters = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
    $numbers = substr(str_shuffle("0123456789"), 0, 4);
    return $letters . $numbers;
}

// Create Discount
if (isset($_POST['create_discount'])) {
    $code = generateRandomCode();
    $amount = $_POST['amount'];
    $qty = $_POST['qty'];

    $insertQuery = "INSERT INTO discounts (code, amount, qty) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($dbConnection, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sdi", $code, $amount, $qty);
    mysqli_stmt_execute($stmt);

    if ($stmt) {
        echo "<div class='notification success'>Discount created successfully with code: $code</div>";
    } else {
        echo "<div class='notification error'>Error: " . mysqli_error($dbConnection) . "</div>";
    }
}

// Update Discount
if (isset($_POST['update_discount'])) {
    $id = $_POST['id'];
    $amount = $_POST['amount'];
    $qty = $_POST['qty'];

    // Check if the discount is fully redeemed
    $checkQuery = "SELECT status FROM discounts WHERE id = ?";
    $stmt = mysqli_prepare($dbConnection, $checkQuery);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $discount = mysqli_fetch_assoc($result);

    if ($discount['status'] == 'fully_redeemed') {
        echo "<div class='notification error'>Cannot edit a fully redeemed discount.</div>";
    } else {
        $updateQuery = "UPDATE discounts SET amount = ?, qty = ? WHERE id = ?";
        $stmt = mysqli_prepare($dbConnection, $updateQuery);
        mysqli_stmt_bind_param($stmt, "dii", $amount, $qty, $id);
        mysqli_stmt_execute($stmt);

        if ($stmt) {
            echo "<div class='notification success'>Discount updated successfully!</div>";
        } else {
            echo "<div class='notification error'>Error: " . mysqli_error($dbConnection) . "</div>";
        }
    }
}

// Delete Discounts
if (isset($_POST['delete_discounts']) && isset($_POST['selected_discounts'])) {
    $ids = $_POST['selected_discounts'];
    foreach ($ids as $id) {
        $deleteQuery = "DELETE FROM discounts WHERE id = ?";
        $stmt = mysqli_prepare($dbConnection, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        if (!$stmt) {
            echo "<div class='notification error'>Error: " . mysqli_error($dbConnection) . "</div>";
        }
    }
    echo "<div class='notification success'>Selected discounts deleted successfully!</div>";
}

// Read Discounts
function readDiscounts($dbConnection) {
    $query = "SELECT * FROM discounts";
    $result = mysqli_query($dbConnection, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><input type='checkbox' class='select-discount' name='selected_discounts[]' value='" . $row['id'] . "'></td>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['code'] . "</td>";
            echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
            echo "<td>" . $row['qty'] . "</td>";
            echo "<td>" . $row['usage_count'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td><button type='button' class='edit-discount' data-id='" . $row['id'] . "' data-amount='" . $row['amount'] . "' data-qty='" . $row['qty'] . "'> <i class='fa-solid fa-pen-to-square'></i> </button></td>";
            echo "</tr>";
        }
    } else {
        echo "<div class='notification error'>Error: " . mysqli_error($dbConnection) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Discounts</title>
    <link rel="icon" type="image/png" href="img/logo.png" />
    <link rel="stylesheet" type="text/css" href="../css/crud-discounts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.edit-discount').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('update_id').value = this.dataset.id;
                    document.getElementById('update_amount').value = this.dataset.amount;
                    document.getElementById('update_qty').value = this.dataset.qty;
                });
            });
        });
    </script>
</head>
<body>
    <div class="main-container">

    <div class="content-wrapper">

    <div class="all">
<h1 class="discount-title">PROMO &amp DISCOUNT</h1>
    <div class="container">
        <form method="POST" action="" class="discount-form">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" required><br>
            <label for="qty">Quantity:</label>
            <input type="number" name="qty" required><br>
            <button type="submit" class="btnAll" name="create_discount">Create Discount</button>
        </form>

        <div class="discount-panel">
        <h2> UPDATE PROMO &amp DISCOUNT</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" id="update_id">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" id="update_amount" required><br>
            <label for="qty">Quantity:</label>
            <input type="number" name="qty" id="update_qty" required><br>
            <button type="submit" class="btnAll" name="update_discount">Update Discount</button>
        </form>
        </div>

        <div class="discount-panel">
        <h2>PROMO &amp DISCOUNT LIST</h2>
        <form method="POST" action="">
            <table>
                <tr>
                    <th>Select</th>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Amount</th>
                    <th>Quantity</th>
                    <th>Usage Count</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php readDiscounts($dbConnection); ?>
            </table>
            <button type="submit" class="btnAll" name="delete_discounts">Delete Selected Discounts</button>
        </form>
        </div>
    </div>
</div>
</div>
</div>
    <?php mysqli_close($dbConnection); ?>
</body>
</html>