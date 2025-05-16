<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('includes/header.php');
include 'db_ec.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $category = $_POST['category'];  // Get category from dropdown

    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_paths = [];

    foreach ($_FILES['product_pictures']['tmp_name'] as $key => $tmp_name) {
        // Generate a unique name based on the current timestamp and product ID (if already available)
        $file_ext = pathinfo($_FILES['product_pictures']['name'][$key], PATHINFO_EXTENSION); // Get file extension
        $unique_name = uniqid('product_' . time() . '_', true) . '.' . $file_ext; // Generate unique name
        
        $target_file = $upload_dir . $unique_name;

        // Move the uploaded file to the desired directory with the new name
        if (move_uploaded_file($tmp_name, $target_file)) {
            $uploaded_paths[] = $target_file;
        }
    }

    $pictures_json = json_encode($uploaded_paths);

    // Insert the product into the database, including the category and the unique file paths
    $stmt = $conn->prepare("INSERT INTO products (user_id, product_name, product_pictures, product_description, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $user_id, $product_name, $pictures_json, $product_description, $category);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Product saved successfully!'); window.location.href='seller.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
</head>
<body class="layout-4">
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <?php include('includes/topnav.php'); ?>
            <?php include('includes/sidebar.php'); ?>

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Create new Products</h1>
                    </div>
                    <div class="section-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                            <!-- Category Dropdown -->
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="1">Agriculture Products</option>
                                    <option value="2">Healthcare Products</option>
                                    <option value="3">Energy Products</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="product_pictures">Product Pictures</label>
                                <input type="file" name="product_pictures[]" class="form-control" multiple required>
                                <small class="form-text text-muted">You can select multiple images.</small>
                            </div>

                            <div class="form-group">
                                <label for="product_description">Product Description</label>
                                <textarea name="product_description" class="form-control" rows="4" required></textarea>
                            </div>

                        

                            <button type="submit" class="btn btn-primary">Submit Product</button>
                        </form>
                    </div>
                </section>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <script src="assets/bundles/lib.vendor.bundle.js"></script>
    <script src="js/CodiePie.js"></script>
    <script src="assets/modules/datatables/datatables.min.js"></script>
    <script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
    <script src="assets/modules/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/modules/sweetalert/sweetalert.min.js"></script>
    <script src="js/page/modules-datatables.js"></script>
    <script src="js/page/modules-sweetalert.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
