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
<script src="assets/bundles/lib.vendor.bundle.js"></script>
<script src="js/CodiePie.js"></script>
<script src="assets/modules/datatables/datatables.min.js"></script>
<script src="assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="assets/modules/sweetalert/sweetalert.min.js"></script>
<script src="js/page/modules-datatables.js"></script>
<script src="js/scripts.js"></script>
<script src="js/custom.js"></script>
<script>
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const dateStr = now.toLocaleDateString('en-US', dateOptions);
        const timeStr = now.toLocaleTimeString('en-US', timeOptions);
        document.getElementById('current-datetime').textContent = `${dateStr} | ${timeStr}`;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();

    document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', () => {
        const name = button.dataset.name;
        const images = JSON.parse(button.dataset.images);
        const likes = button.dataset.likes;
        const productId = button.dataset.id;

        // Set modal info
        let currentImageIndex = 0;
        document.getElementById('modalProductName').textContent = name;
        document.getElementById('modalLikes').textContent = likes;
        document.getElementById('modalImage').src = images[currentImageIndex];
        document.getElementById('editProductBtn').href = 'seller_edit.php?id=' + productId;

        // Show modal
        $('#productModal').modal('show');

        // Image Viewer and Floating Buttons
        const prevBtn = document.getElementById('prevImage');
        const nextBtn = document.getElementById('nextImage');
        const img = document.getElementById('modalImage');

        function updateImageView() {
            if (images.length > 0) {
                img.src = images[currentImageIndex]; // Set the current image

                // Show buttons only if there’s more than one image
                prevBtn.style.display = images.length > 1 && currentImageIndex > 0 ? 'block' : 'none';
                nextBtn.style.display = images.length > 1 && currentImageIndex < images.length - 1 ? 'block' : 'none';
            } else {
                img.style.display = 'none';
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            }
        }

        updateImageView(); // Initially update the image view

        // Next/Previous image navigation
        nextBtn.addEventListener('click', () => {
            if (currentImageIndex < images.length - 1) {
                currentImageIndex++;
                updateImageView();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                updateImageView();
            }
        });
    });
});

</script>
</body>
</html>
