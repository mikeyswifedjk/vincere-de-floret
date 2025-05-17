<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';

if (isset($_GET['ids']) && !empty($_GET['ids'])) {
    $selectedIds = explode(',', $_GET['ids']);

    // Get the categories of the deleted products along with the count
    $deletedProductCategories = [];
    foreach ($selectedIds as $id) {
        $categoryQuery = mysqli_query($conn, "SELECT category_id, image FROM pots WHERE id = '$id'");
        $categoryRow = mysqli_fetch_assoc($categoryQuery);
        $category_id = $categoryRow['category_id'];
        $imagePath = $categoryRow['image']; // Save the image path to delete it later
        
        // Delete the image file from the server if it exists
        if (file_exists('../img/' . $imagePath)) {
            unlink('../img/' . $imagePath); // Delete the image from server
        }

        $deletedProductCategories[$category_id] = isset($deletedProductCategories[$category_id]) ? $deletedProductCategories[$category_id] + 1 : 1;
    }

    foreach ($selectedIds as $id) {
        // Use prepared statements to delete the product
        $deleteProductQuery = "DELETE FROM pots WHERE id = ?";
        $stmt = mysqli_prepare($conn, $deleteProductQuery);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            // Product deleted successfully
            mysqli_stmt_close($stmt);
        } else {
            echo "Error deleting pots with ID $id: " . mysqli_error($conn);
        }
    }

    // Update product_count in the category table based on the count of deleted products
    foreach ($deletedProductCategories as $category_id => $deletedCount) {
        $updateQuery = "UPDATE category SET product_count = product_count - $deletedCount WHERE id = '$category_id'";
        if (mysqli_query($conn, $updateQuery)) {
            echo "Category $category_id updated successfully. ";
        } else {
            echo "Error updating category $category_id: " . mysqli_error($conn);
        }
    }

    // Set a success message in the session
    session_start();
    $_SESSION['success_message'] = "Selected pots were successfully deleted.";

    // Redirect back to the product list page
    header('Location: add-pots.php');
    exit();
} else {
    echo "No product IDs provided for deletion.";
}
?>