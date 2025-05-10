<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

// Process the form submission
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $category = $_POST["category"];
    $price = $_POST["price"];
    $totalQty = $_POST["qty"];

    try {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script> alert('Invalid Image Extension'); </script>";
        } else if ($fileSize > 5000000) {
            echo "<script> alert('Image Size Is Too Large'); </script>";
        } else {
            $newImageName = uniqid();
            $newImageName .= '.' . $imageExtension;

            $res = move_uploaded_file($tmpName, '../img/' . $newImageName);
            if ($res) {
                // Insert pots data without variants
                $query = "INSERT INTO pots (name, image, category, qty, price, category_id) 
                          VALUES ('$name', '$newImageName', '$category', '$totalQty', '$price', 
                                  (SELECT id FROM category WHERE category = '$category'))";

                if (mysqli_query($conn, $query)) {
                    $potsID = mysqli_insert_id($conn);

                    // Increment pots_count
                    mysqli_query($conn, "UPDATE category SET pots_count = pots_count + 1 
                                         WHERE id = (SELECT id FROM category WHERE category = '$category')");

                    echo "<script>alert('Successfully Added'); document.location.href = 'add-pots.php';</script>";
                } else {
                    echo "Error: " . $query . "<br>" . mysqli_error($conn);
                }
            } else {
                echo "Failed to upload";
            }
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add pots - Admin Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="../css/add-pots.css">
</head>
<body>

    <div class="main-container">
    <?php include('admin-nav.php'); ?> 

    <div class="content-wrapper">
    <div class="all">
    <!-- Tab Navigation -->
    <div class="tab-container">
        <div class="pots-tab"><a href="add-product.php">Package</a></div>
        <div class="flower-tab"><a href="add-flower.php">Flower</a></div>
        <div class="add-ons-tab"><a href="add-addons.php">Add-Ons</a></div>
        <div class="pots-tab"><a href="add-pots.php">Pots</a></div>
    </div>

    <!-- Add pots Section -->
    <h1 class="text1">Package Deal</h1>
    <div class="add">
        <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
            <label for="name">Package Name:</label>
            <input type="text" name="name" id="name" required autocomplete="name"><br><br>

            <label for="image">Package Image:</label>
            <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png, .webp, .avif"
                   autocomplete="file" onchange="previewImage(this);" required><br><br>

            <label for="category">Category:</label>
            <select name="category" required>
                <option value="" disabled selected>Select Category</option>
                <?php
                $categories = mysqli_query($conn, "SELECT category FROM category");
                while ($row = mysqli_fetch_assoc($categories)) {
                    echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                }
                ?>
            </select><br><br>

            <label for="price">Price:</label>
            <input type="text" name="price" id="price" required autocomplete="number"><br><br>

            <label for="qty">Quantity:</label>
            <input type="text" name="qty" id="qty" required autocomplete="number"><br><br>

            <button type="submit" name="submit" class="buttonpots">Add Package</button>
        </form>
    </div>

    <!-- Image pots Upload Preview -->
    <div class="imageProd">
        <img src="no-image.webp" id="imagePreview" alt="Image Preview">
    </div>

    <!-- pots List Section -->
    <div class="view">
        <h1 class="text4">Package Deal List</h1>

        <div class="table-controls">
        <!-- Search pots -->
        <form action="" method="post" class="search-form">
            <input type="text" name="search" id="search" placeholder="Enter pots name" required>
            <button type="submit" name="search_submit" class="btnSearch">Search</button>
        </form>
        </div>

        <!-- pots Table -->
         
        <!-- Delete Form -->
        <form action="delete-multiple.php" method="post" id="deleteForm">
                    <button type="submit" class="deletebtn" onclick="deletepotss();">Delete Selected</button>
                </form>
        <table border="1" cellspacing="0" cellpadding="10" class="viewTable">
            <tr class="thView">
                <th>ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Category</th>
                <th>Price</th>
                <th>Qty</th>
                <th colspan="2">Actions</th>
            </tr>

            <?php
            if (isset($_POST['search_submit'])) {
                $search = $_POST['search'];
                $rows = mysqli_query($conn, "SELECT * FROM pots WHERE name LIKE '%$search%'");
            } else {
                $rows = mysqli_query($conn, "SELECT * FROM pots");
            }

            foreach ($rows as $row) :
            ?>
                <tr>
                    <td><?= $row["id"]; ?></td>
                    <td><?= $row["name"]; ?></td>
                    <td><img src="../img/<?= $row['image']; ?>" width="100px" alt="pots"></td>
                    <td><?= $row["category"]; ?></td>
                    <td>₱<?= $row["price"]; ?></td>
                    <td><?= $row["qty"]; ?></td>
                    <td>
                        <button class="editbtn" onclick="editpots(<?= $row['id']; ?>)">Edit</button>
                    </td>
                    <td>
                        <input type="checkbox" name="delete[]" value="<?= $row["id"]; ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div> 
</div>
</div>
</div>

<script>
    function editpots(potsId) {
        window.open('edit-pots.php?id=' + potsId, '_self');
    }

    function deletepotss() {
        var selectedpotss = document.querySelectorAll('input[name="delete[]"]:checked');
        var selectedIds = Array.from(selectedpotss).map(function (pots) {
            return pots.value;
        });

        if (selectedIds.length > 0) {
            if (confirm("Are you sure you want to delete these potss? Items you delete can't be restored")) {
                document.getElementById('deleteForm').action = 'delete-pots.php?ids=' + selectedIds.join(',');
            } else {
                return false;
            }
        } else {
            alert("Please select at least one pots to delete.");
            return false;
        }
    }

    function previewImage(input) {
        var preview = document.getElementById('imagePreview');
        var file = input.files[0];
        var reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "no-image.webp";
        }
    }
</script>

</body>
</html>
