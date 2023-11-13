<?php
// update.php

require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    global $db_connect;

    $productId = $_POST['id'];
    $productName = $_POST['name'];
    $productPrice = $_POST['price'];

    // File upload handling
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/utspwb/upload/';

    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Define $randomFilename here
    $randomFilename = time() . '-' . md5(rand()) . '-';

    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $tempImage = $_FILES['image']['tmp_name'];

        // Append the original file name to $randomFilename
        $randomFilename .= $image;

        $uploadPath = $uploadDir . $randomFilename;

        $upload = move_uploaded_file($tempImage, $uploadPath);

        if ($upload) {
            // Delete the old image file if it exists
            $oldImageQuery = mysqli_query($db_connect, "SELECT image FROM products WHERE id = $productId");
            $oldImagePath = mysqli_fetch_assoc($oldImageQuery)['image'];

            if ($oldImagePath) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $oldImagePath);
            }

            // Update the product with the new image path
            mysqli_query($db_connect, "UPDATE products SET name = '$productName', price = '$productPrice', image = '/utspwb/upload/$randomFilename' WHERE id = $productId");

            echo "Product updated successfully with a new image.";
        } else {
            echo "Error uploading a new image.";
        }
    } else {
        // If no new image is uploaded, update the product without changing the image path
        mysqli_query($db_connect, "UPDATE products SET name = '$productName', price = '$productPrice' WHERE id = $productId");

        echo "Product updated successfully without changing the image.";
    }
} else {
    echo "Invalid request.";
}
