<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('admin-nav.php'); 
require ('connection.php');

// Function to validate if a value is a valid integer
function isValidInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

// Check if the form is submitted to create a new category
if (isset($_POST["submit"])) {
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $fee = mysqli_real_escape_string($conn, $_POST["fee"]);

    if (isValidInteger($fee)) {
        $query = "INSERT INTO shipping (address, fee) VALUES('$address', '$fee')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Successfully Added');</script>";
        } else {
            echo "<script>alert('Error adding record: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Amount Fee must be a valid number');</script>";
    }
}

// Edit Category
if (isset($_POST["edit"])) {
    $editCategoryId = mysqli_real_escape_string($conn, $_POST["edit_id"]);
    $editAddress = mysqli_real_escape_string($conn, $_POST["edit_address"]);
    $editFee = mysqli_real_escape_string($conn, $_POST["edit_fee"]);

    if (isValidInteger($editFee)) {
        $editQuery = "UPDATE shipping SET address = '$editAddress', fee = '$editFee' WHERE id = $editCategoryId";
        if (mysqli_query($conn, $editQuery)) {
            echo "<script>alert('Category Updated Successfully');</script>";
        } else {
            echo "<script>alert('Error updating record: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Amount Fee must be a valid number');</script>";
    }
}

// Delete Selected Categories
if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_categories"]) && is_array($_POST["selected_categories"])) {
        foreach ($_POST["selected_categories"] as $selectedCategoryId) {
            $deleteQuery = "DELETE FROM shipping WHERE id = $selectedCategoryId";
            if (!mysqli_query($conn, $deleteQuery)) {
                echo "<script>alert('Error deleting record: " . mysqli_error($conn) . "');</script>";
            }
        }
        echo "<script>alert('Selected Categories Deleted Successfully');</script>";
    } else {
        echo "<script>alert('No categories selected for deletion');</script>";
    }
}

// Define a variable for the search term
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$searchQuery = "SELECT * FROM shipping WHERE address LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $searchQuery);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo.png"/>
    <link rel="stylesheet" type="text/css" href="css/crud-shipping.css">
    <title>SHIPPING</title>
</head>
<body>
    <h1 class="text1">SHIPPING MANAGEMENT</h1>
    <div class="all">
        <div class="add">
            <form action="" method="post" autocomplete="off">
                <label for="address">LOCATION: </label>
                <input type="text" name="address" id="address" required placeholder="Enter location"> <br> <br>
                <label for="fee">AMOUNT FEE: </label>
                <input type="text" name="fee" id="fee" required placeholder="Enter amount fee"> <br> <br>
                <button type="submit" name="submit" class="btnSubmit">Submit</button>
            </form>
        </div> <!-- add -->

        <div class="view">
            <!-- Search Form -->
            
            <form action="" method="get" class="searchForm">
                <h2 class="text4">SHIPPING RATES</h2>
                <input type="text" name="search" class="searchtxt" id="search" placeholder="Enter address..." required />
                <button type="submit" class="btnSearch">
                    <i class="fa-solid fa-magnifying-glass" style="color: #502779;"></i>
                </button>
            </form> <br>

            <form action="" method="POST">
                <button type="submit" name="delete_selected" class="deletebtn">
                    <i class="fa-solid fa-trash" style="color: #AD53A6"></i>
                </button>
                <table border="1" cellspacing="0" cellpadding="10">

                    <tr class="thView">
                        <th>ID</th>
                        <th>Location</th>
                        <th>Amount Fee</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['fee']; ?></td>
                        <td class="editForm">
                            <!-- Edit Form -->
                            <form action="" method="post">
                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                <label for="edit_address">Location:</label>
                                <input type="text" name="edit_address" class="input" value="<?php echo $row['address']; ?>" required><br><br>
                                <label for="edit_fee">Amount Fee: </label>
                                <input type="text" name="edit_fee" class="input" value="<?php echo $row['fee']; ?>" required><br><br>
                                <button type="submit" name="edit" class="editbtn">
                                    <i class="fa-solid fa-pen-to-square" style="color:white"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <input type="checkbox" name="selected_categories[]" value="<?php echo $row['id']; ?>">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
              
            </form>
        </div>
    </div> 
</body>
</html>