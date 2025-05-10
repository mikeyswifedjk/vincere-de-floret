<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('admin-nav.php');

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $stocks = $_POST["stocks"];
    $price = $_POST["price"]; 

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $validImageExtension) && $fileSize <= 1000000) {
            $newImageName = uniqid() . '.' . $imageExtension;
            $res = move_uploaded_file($tmpName, '../img/' . $newImageName);
            if ($res) {
                $query = "INSERT INTO pots (pots, image, stocks, price) VALUES('$name', '$newImageName', '$stocks', '$price')";
                mysqli_query($conn, $query);
                echo "<script>alert('Successfully Added');</script>";
            } else {
                echo "Failed to upload";
            }
        } else {
            echo "<script>alert('Invalid Image Extension or Image Size Is Too Large');</script>";
        }
    } else {
        echo "<script>alert('Image Upload Error');</script>";
    }
}

$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchQuery = "SELECT * FROM pots WHERE pots LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $searchQuery);

if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_categories"]) && is_array($_POST["selected_categories"])) {
        foreach ($_POST["selected_categories"] as $selectedpotsId) {
            $deleteQuery = "DELETE FROM pots WHERE id = $selectedpotsId";
            mysqli_query($conn, $deleteQuery);
        }
        echo "<script>alert('Selected pots deleted successfully');</script>";
    } else {
        echo "<script>alert('No pots selected for deletion');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/add-pots.css">
    <title>Pots Management - Sunny Bloom</title>
</head>
<body>
<div class="main-container">

    <div class="content-wrapper">

    <div class="all">
         <!-- Tab Navigation -->
    <div class="tab-container">
        <div class="product-tab"><a href="add-product.php">Package</a></div>
        <div class="flower-tab"><a href="add-flower.php">Flower</a></div>
        <div class="add-ons-tab"><a href="add-addons.php">Add-Ons</a></div>
        <div class="pots-tab"><a href="add-pots.php">Pots</a></div>
    </div>

        <!-- Pots Management Header -->
        <h1 class="text1">Pots Management</h1>

        <!-- Add Pots Form -->
        <div class="add">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="name">Pots Name:</label>
                <input type="text" name="name" placeholder="Pots name" required><br><br>

                <label for="stocks">Stocks:</label>
                <input type="text" name="stocks" placeholder="Stocks" required><br><br>

                <label for="price">Price:</label>
                <input type="text" name="price" placeholder="Price" required><br><br>

                <label for="image">Image:</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png" required><br><br>

                <button type="submit" name="submit" class="buttonProduct">Add Pots</button>
            </form>
        </div>

        <!-- Pots List and Search Section -->
        <div class="view">
            <h1 class="text4">Pots List</h1>
            <div class="table-controls">
                <form method="get" class="search-form">
                    <input type="text" name="search" placeholder="Search pots name" value="<?= $searchTerm ?>">
                    <button type="submit" class="btnSearch">Search</button>
                </form>
            </div>

            <!-- Pots List Table -->
            <form method="post">
            <button type="submit" name="delete_selected" class="deletebtn">Delete Selected</button>
                <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Stocks</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th colspan="2">Action</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['pots'] ?></td>
                        <td><?= $row['stocks'] ?></td>
                        <td><?= $row['price'] ?></td>
                        <td><img src="../img/<?= $row['image'] ?>" height="80"></td>
                        <td><a href="edit-pots.php?id=<?= $row['id'] ?>" class="editbtn">Edit</a></td>
                        <td><input type="checkbox" name="selected_categories[]" value="<?= $row['id'] ?>"></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </form>
        </div>
    </div>
</body>
</html>