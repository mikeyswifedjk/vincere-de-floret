<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve product details using prepared statements
    $selectProductQuery = "SELECT * FROM flower WHERE id = ?";
    $stmtProduct = mysqli_prepare($conn, $selectProductQuery);
    mysqli_stmt_bind_param($stmtProduct, 'i', $id);
    if (mysqli_stmt_execute($stmtProduct)) {
        $result = mysqli_stmt_get_result($stmtProduct);
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "Error fetching product details: " . mysqli_stmt_error($stmtProduct);
    }
    mysqli_stmt_close($stmtProduct);

    // Check if a product with the specified ID exists
    if ($product) {
        if (isset($_POST['submit'])) {
            // Handle the form submission
            $newName = mysqli_real_escape_string($conn, $_POST['name']);
            $newCategory = mysqli_real_escape_string($conn, $_POST['category']);
            $newPrice = mysqli_real_escape_string($conn, $_POST['prices']);
            $newQty = mysqli_real_escape_string($conn, $_POST['qtys']);

            // Check if a new image is being uploaded
            if (!empty($_FILES['image']['name'])) {
                $newImage = $_FILES['image']['name'];

                // Specify the directory where you want to save the uploaded image
                $uploadDir = '../img/';

                // Get the temporary file name
                $tempName = $_FILES['image']['tmp_name'];

                // Create a unique name for the image
                $newImageName = time() . '_' . $newImage;

                // Move the uploaded image to the destination directory
                if (move_uploaded_file($tempName, $uploadDir . $newImageName)) {
                    // Update the product details, including the new image path
                    $updateProductQuery = "UPDATE flower SET name = ?, category = ?, image = ?, price = ?, qty = ? WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $updateProductQuery);
                    mysqli_stmt_bind_param($stmt, 'ssssii', $newName, $newCategory, $newImageName, $newPrice, $newQty, $id);
                    mysqli_stmt_execute($stmt);
                }
            } else {
                // No new image uploaded, update product details without changing the image
                $updateProductQuery = "UPDATE flower SET name = ?, category = ?, price = ?, qty = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $updateProductQuery);
                mysqli_stmt_bind_param($stmt, 'ssssi', $newName, $newCategory, $newPrice, $newQty, $id);
                mysqli_stmt_execute($stmt);
            }

            echo "<script>alert('Flowers Updated Successfully'); document.location.href = 'add-flowers.php';</script>";
        }
    } else {
        echo '<script>alert("Flowers not found with ID: ' . $id . '");</script>';
    }
} else {
    echo '<script>alert("Flowers ID not provided");</script>';
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/edit-product.css"> 
    <title>Vincere De Floret</title>
</head>
<body>
    <div class="main-container">
    <?php include('admin-nav.php'); ?>

    <div class="content-wrapper">
    <div class="all">
        <h1 class="title">Update Flowers</h1>

        <form action="" method="post" name="product_form" autocomplete="off" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="name">Flowers Name</label>
                <input type="text" name="name" id="name" required value="<?= $product['name']; ?>">
            </div>

            <div class="form-group">
                <label>Flowers Image:</label>
                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                <?php if (!empty($product['image'])): ?>
                    <img class="preview-image" src="../img/<?= $product['image']; ?>" alt="Current Image">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <?php
                    $categoryQuery = mysqli_query($conn, "SELECT DISTINCT category FROM category");
                    while ($categoryRow = mysqli_fetch_assoc($categoryQuery)) {
                        $selected = ($categoryRow['category'] == $product['category']) ? "selected" : "";
                        echo "<option value='{$categoryRow['category']}' $selected>{$categoryRow['category']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="prices">Price</label>
                <input type="text" name="prices" id="prices" value="<?= $product['price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="qtys">Quantity</label>
                <input type="text" name="qtys" id="qtys" value="<?= $product['qty']; ?>" required>
            </div>

            <div class="form-group">
                <button class="submit-btn" type="submit" name="submit">Update</button>
            </div>
        </form>
        </div>
    </div>
    </div>
</body>
</html>
