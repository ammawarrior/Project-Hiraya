<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_ec.php';

// Fetch products belonging to the logged-in user
$query = "SELECT product_id, product_name, product_pictures, status, likes FROM products WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
    <link rel="stylesheet" href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="icon" type="image/png" href="assets/img/dost.png">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <style>
body, html {
    overflow-x: hidden;
}

/* Force table to fit in screen */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Optional: shrink large elements in table */
@media (max-width: 768px) {
    .table td, .table th {
        white-space: nowrap;
        font-size: 14px;
    }

    .view-btn {
        font-size: 12px;
        padding: 4px 8px;
    }

    .dataTables_wrapper .dataTables_filter {
        float: left;
        text-align: left;
        margin-top: 10px;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-top: 10px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: left;
        width: 100%;
        text-align: center;
        margin-top: 10px;
    }
}
</style>

</head>
<body class="layout-4">
<div class="page-loader-wrapper">
    <span class="loader"><span class="loader-inner"></span></span>
</div>
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <?php include('includes/topnav.php'); include('includes/sidebar.php'); ?>
        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h1>Product Dashboard</h1>
                    <div id="current-datetime" style="font-size: 1.1rem; font-weight: 500;"></div>
                </div>

                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4>My Product Listings</h4>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='createproduct.php'">
                                        Submit New Product
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="table-1">
                                            <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $statusLabel = '';
                                                    switch ($row['status']) {
                                                        case 1:
                                                            $statusLabel = '<span class="badge badge-warning">Pending</span>';
                                                            break;
                                                        case 2:
                                                            $statusLabel = '<span class="badge badge-success">Confirmed</span>';
                                                            break;
                                                        case 3:
                                                            $statusLabel = '<span class="badge badge-danger">Rejected</span>';
                                                            break;
                                                        default:
                                                            $statusLabel = '<span class="badge badge-secondary">Unknown</span>';
                                                    }

                                                    // Prepare image paths
                                                    $pictures = json_decode(stripslashes($row['product_pictures']), true);

                                                    // Pass product info via data attributes
                                                    echo "<tr>
                                                        <td>{$row['product_name']}</td>
                                                        <td>{$statusLabel}</td>
                                                        <td>
                                                            <button 
    class='btn btn-primary btn-sm view-btn' 
    data-id='{$row['product_id']}'
    data-name='{$row['product_name']}'
    data-images='" . json_encode($pictures) . "' 
    data-likes='{$row['likes']}'>
    View
</button>

                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center'>No products found</td></tr>";
                                            }
                                            $stmt->close();
                                            $conn->close();
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include('includes/footer.php'); ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Image Viewer Container -->
                <div id="imageViewer" style="position: relative; max-width: 100%; margin-bottom: 15px;">
                    <img id="modalImage" src="" alt="Product Image" style="display: block; width: 100%; max-height: 300px; object-fit: contain; border-radius: 8px;">
                    
                    <!-- Floating Prev and Next buttons -->
                    <button type="button" id="prevImage" class="btn btn-light" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); background-color: rgba(0,0,0,0.4); color: white; border: none; display: none; font-size: 1.5rem; padding: 5px 10px; border-radius: 50%;">⟨</button>
                    <button type="button" id="nextImage" class="btn btn-light" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background-color: rgba(0,0,0,0.4); color: white; border: none; display: none; font-size: 1.5rem; padding: 5px 10px; border-radius: 50%;">⟩</button>
                </div>

                <h5 id="modalProductName"></h5>
                <p><i class="fas fa-heart text-danger"></i> <span id="modalLikes"></span> Likes</p>
            </div>
            <div class="modal-footer">
    <a id="editProductBtn" href="#" class="btn btn-primary">Edit this Product</a>
</div>

        </div>
    </div>
</div>


<!-- Scripts -->
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

<script src="assets/bundles/lib.vendor.bundle.js"></script>
<script src="js/CodiePie.js"></script>
<script src="assets/modules/datatables/datatables.min.js"></script>
<script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/modules/sweetalert/sweetalert.min.js"></script>
<script src="js/scripts.js"></script>
<script src="js/custom.js"></script>
<script>
$(document).ready(function () {
    $('#table-1').DataTable({
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 2 }
        ]
    });



    $(document).on('click', '.view-btn', function () {
        const button = this;
        const name = button.dataset.name;
        const images = JSON.parse(button.dataset.images);
        const likes = button.dataset.likes;
        const productId = button.dataset.id;

        let currentImageIndex = 0;
        $('#modalProductName').text(name);
        $('#modalLikes').text(likes);
        $('#modalImage').attr('src', images[currentImageIndex]);
        $('#editProductBtn').attr('href', 'seller_edit.php?id=' + productId);
        $('#productModal').modal('show');

        function updateImageView() {
            $('#modalImage').attr('src', images[currentImageIndex]);
            $('#prevImage').toggle(images.length > 1 && currentImageIndex > 0);
            $('#nextImage').toggle(images.length > 1 && currentImageIndex < images.length - 1);
        }

        $('#nextImage').off('click').on('click', () => {
            if (currentImageIndex < images.length - 1) {
                currentImageIndex++;
                updateImageView();
            }
        });

        $('#prevImage').off('click').on('click', () => {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                updateImageView();
            }
        });

        updateImageView();
    });
});
</script>

</body>
</html>
