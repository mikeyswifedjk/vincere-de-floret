<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('admin-nav.php');
if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid request'); window.location.href='add-flower.php';</script>";
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM flower WHERE id = $id");

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Flower not found'); window.location.href='add-flower.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = $_POST["name"];
    $stocks = $_POST["stocks"];
    $price = $_POST["price"];
    $existingImage = $_POST["existing_image"];
    $imageToSave = $existingImage;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, ['jpg', 'jpeg', 'png']) && $fileSize <= 1000000) {
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, '../img/' . $newImageName);

            if (file_exists('../img/' . $existingImage)) {
                unlink('../img/' . $existingImage);
            }

            $imageToSave = $newImageName;
        } else {
            echo "<script>alert('Invalid image or too large');</script>";
        }
    }

    mysqli_query($conn, "UPDATE flower SET flower='$name', stocks='$stocks', price='$price', image='$imageToSave' WHERE id=$id");
    echo "<script>alert('Flower updated successfully'); window.location.href='add-flower.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/edit-flower.css">
    <title>Update Flower Details - Sunny Bloom</title>
</head>
<body>
    <div class="main-container">
    <div class="content-wrapper">
    <div class="all">
    <h1 class="title">Update Flower Details</h1>
    <form method="post" autocomplete="off" enctype="multipart/form-data" class="form">

        <div class="form-group">
        <label for="name">Flower Name:</label>
        <input type="text" name="name" value="<?= $row['flower'] ?>" required>
        </div>

        <div class="form-group">
        <label for="stocks">Stocks:</label>
        <input type="text" name="stocks" value="<?= $row['stocks'] ?>" required>
        </div>

        <div class="form-group">
        <label for="price">Price:</label>
        <input type="text" name="price" value="<?= $row['price'] ?>" required>
        </div>

        <div class="form-group">
        <label for="image">Current Image:</label>
        <img src="../img/<?= $row['image'] ?>" class="preview-image">
        </div>
        
        <label>Change Image (optional):</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png">

        <div class="form-group">
        <button class="submit-btn" type="submit" name="update">Update</button>
        </div>
    </form>
    </div>
    </div>
    </div>
</body>
</html>