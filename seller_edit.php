<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/header.php');
include 'db_ec.php';

if (!isset($_GET['id'])) {
    echo "Invalid product ID.";
    exit();
}
$product_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch product details
$stmt = $conn->prepare("SELECT product_name, product_description, category, product_pictures FROM products WHERE product_id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Product not found or you don't have permission.";
    exit();
}

$product = $result->fetch_assoc();
$existing_images = json_decode($product['product_pictures'], true);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $category = $_POST['category'];
    $upload_dir = 'uploads/';
    $new_uploaded_paths = [];

    // Handle new uploads
    if (!empty($_FILES['product_pictures']['tmp_name'][0])) {
        foreach ($_FILES['product_pictures']['tmp_name'] as $key => $tmp_name) {
            $file_ext = pathinfo($_FILES['product_pictures']['name'][$key], PATHINFO_EXTENSION);
            $unique_name = uniqid('product_' . time() . '_', true) . '.' . $file_ext;
            $target_file = $upload_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $new_uploaded_paths[] = $target_file;
            }
        }
    }

    // Handle removed images
    $removed_images = json_decode($_POST['removed_images'] ?? '[]', true);
    $remaining_images = array_filter($existing_images, function($img) use ($removed_images) {
        return !in_array($img, $removed_images);
    });

    // Optional: delete removed image files from server
    foreach ($removed_images as $imgPath) {
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
    }

    $all_images = array_merge($remaining_images, $new_uploaded_paths);
    $pictures_json = json_encode(array_values($all_images));

    $update_stmt = $conn->prepare("UPDATE products SET product_name = ?, product_description = ?, category = ?, product_pictures = ? WHERE product_id = ? AND user_id = ?");
    $update_stmt->bind_param("ssissi", $product_name, $product_description, $category, $pictures_json, $product_id, $user_id);
    $update_stmt->execute();

    echo "<script>alert('Product updated successfully!'); window.location.href='seller.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/modules/datatables/datatables.min.css">
    <style>
        .image-wrapper {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .remove-image {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 16px;
            line-height: 1;
            padding: 0 6px;
            cursor: pointer;
        }
        .remove-image:hover {
            background: darkred;
        }
    </style>
</head>
<body class="layout-4">
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <?php include('includes/topnav.php'); ?>
            <?php include('includes/sidebar.php'); ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Edit Product</h1>
                    </div>
                    <div class="section-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="1" <?= $product['category'] == 1 ? 'selected' : '' ?>>Agriculture Products</option>
                                    <option value="2" <?= $product['category'] == 2 ? 'selected' : '' ?>>Healthcare Products</option>
                                    <option value="3" <?= $product['category'] == 3 ? 'selected' : '' ?>>Energy Products</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Existing Images</label>
                                <div class="d-flex flex-wrap" id="existing-images">
                                    <?php foreach ($existing_images as $img): ?>
                                        <div class="image-wrapper">
                                            <img src="<?= htmlspecialchars($img) ?>" style="max-height: 100px;">
                                            <button type="button" class="remove-image" data-img="<?= htmlspecialchars($img) ?>">&times;</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="removed_images" id="removed-images">
                            </div>

                            <div class="form-group">
                                <label>Add More Pictures</label>
                                <input type="file" name="product_pictures[]" class="form-control" multiple>
                                <small>You can select additional images to upload.</small>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="product_description" class="form-control" rows="4" required><?= htmlspecialchars($product['product_description']) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </section>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <script>
        const removedImages = new Set();
        document.querySelectorAll('.remove-image').forEach(button => {
            button.addEventListener('click', function () {
                const wrapper = this.closest('.image-wrapper');
                const imgPath = this.getAttribute('data-img');
                removedImages.add(imgPath);
                wrapper.remove();
                document.getElementById('removed-images').value = JSON.stringify([...removedImages]);
            });
        });
    </script>

    <script src="assets/bundles/lib.vendor.bundle.js"></script>
</body>
</html>
