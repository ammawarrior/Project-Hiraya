<?php 
include 'config/database.php';
include 'layout/header.php';
?>
<style>
    /* Product Card Animations */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* Image Hover Effects */
    .product-image {
        transition: transform 0.3s ease;
    }
    .product-image:hover {
        transform: scale(1.05);
    }

    /* Seller Info Hover */
    .seller-info {
        transition: all 0.3s ease;
    }
    .seller-info:hover {
        background-color: rgba(0,0,0,0.05);
    }

    /* Description Toggle */
    .description-container {
        max-height: 200px;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    .description-container.expanded {
        max-height: none;
    }

    /* Button Animations */
    .expand-button {
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .expand-button:hover {
        color: var(--primary-color);
    }

    /* Related Products Grid */
    .related-products-grid {
        transition: all 0.3s ease;
    }
    .related-products-grid:hover {
        transform: scale(1.02);
    }

    /* Overlay Effects */
    .uk-overlay-xlight {
        background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.2));
    }

    /* Pure CSS Scroll Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .seller-info {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }

    .product-card {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }

    .uk-button-primary {
        animation: fadeIn 0.5s ease-out forwards 0.3s;
        opacity: 0;
    }
</style>
<?php
// Check if product ID is provided
if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch product details with seller information
$stmt = $pdo->prepare("SELECT p.*, u.username, u.user_picture, u.email 
                      FROM products p 
                      JOIN users u ON p.user_id = u.user_id 
                      WHERE p.product_id = ? AND p.status IN (1, 2)");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// echo "Product ID: " . htmlspecialchars($product_id) . "<br>";
if (!$product) {
    echo "No product found with this ID.";
}

// Fetch related products (same category)
$stmt = $pdo->prepare("SELECT p.*, u.username 
                      FROM products p 
                      JOIN users u ON p.user_id = u.user_id 
                      WHERE p.category = ? 
                      AND p.product_id != ? 
                      AND p.status = 'active' 
                      LIMIT 3");
$stmt->execute([$product['category'], $product_id]);
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process product pictures (JSON-encoded array)
$product_images = json_decode($product['product_pictures'], true);
if (!is_array($product_images)) {
    $product_images = [];
}

// Get first image path
$first_image = !empty($product_images[0]) && file_exists($product_images[0]) 
    ? $product_images[0]
    : 'img/agriculture.jpg'; // Default image if none exists

// Remove quotes and slashes from JSON path
$first_image = str_replace(['\\', '"'], ['', ''], $first_image);

?>

<div class="uk-section uk-margin-top">
    <div class="uk-container">
        <div class="uk-grid-match" data-uk-grid>
            <!-- Product Images -->
            <div class="uk-width-1-2@m">
                <div class="uk-card uk-card-default uk-card-body uk-border-rounded-large">
                    <?php if (count($product_images) > 1): ?>
                    <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" data-uk-slider>
                        <ul class="uk-slider-items uk-child-width-1-1">
                            <?php foreach ($product_images as $image): ?>
                            <li>
                                <img src="<?php echo htmlspecialchars(trim($image)); ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     class="uk-width-1-1">
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-previous data-uk-slider-item="previous"></a>
                        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-next data-uk-slider-item="next"></a>
                    </div>
                    <?php else: ?>
                    <img src="<?php echo htmlspecialchars($first_image); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="uk-width-1-1">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="uk-width-1-2@m">
                <div class="uk-card uk-card-default uk-card-body uk-border-rounded-large">
                    <h1 class="uk-heading-medium"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    <div class="uk-margin">
                        <!-- Removed price display -->
                    </div>
                    
                    <!-- Seller Information -->
<div class="uk-margin">
    <div class="uk-grid-small" data-uk-grid>
        <div class="uk-width-auto">
            <?php 
            // Set default user picture if none is provided
            $user_picture = !empty($product['user_picture']) && file_exists("uploads/" . $product['user_picture'])
                ? "uploads/" . $product['user_picture']
                : 'img/profile_null.png';
            ?>
            <img class="uk-border-circle" width="50" height="50" 
                 src="<?php echo htmlspecialchars($user_picture); ?>" 
                 alt="<?php echo htmlspecialchars($product['username']); ?>" 
                 style="border-radius: 50%; width: 50px; height: 50px; object-fit: cover;">
        </div>
        <div class="uk-width-expand">
            <h3 class="uk-card-title uk-margin-remove-bottom"><?php echo htmlspecialchars($product['username']); ?></h3>
            <p class="uk-text-meta uk-margin-remove-top">Seller</p>
        </div>
    </div>
</div>
                    
                    <!-- Product Description -->
                    <div class="uk-margin description-container">
                        <h3>Description</h3>
                        <div class="uk-text-break">
                            <?php 
                            // Convert newlines to <br> tags and preserve other HTML formatting
                            $description = nl2br($product['product_description']);
                            // Allow basic HTML formatting but prevent XSS
                            $description = strip_tags($description, '<br><p><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6>');
                            echo $description;
                            ?>
                        </div>
                    </div>
                    
                    <!-- Category and Likes -->
                    <div class="uk-margin">
                        <div class="uk-grid-small" data-uk-grid>
                            <div>
                                <span class="uk-text-muted"><?php echo $product['likes']; ?> likes</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="uk-margin">
                        <a href="contact.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="uk-button uk-button-secondary uk-width-1-1 uk-box-shadow-medium uk-animation-scale-up" data-uk-scrollspy="cls: uk-animation-scale-up; delay: 300">
                            Contact Seller
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="uk-margin-large-top">
            <h2>Related Products</h2>
            <div class="uk-child-width-1-3@m uk-grid-match uk-grid-medium related-products-grid" data-uk-grid data-uk-scrollspy="cls: uk-animation-slide-left; target: > div > div; delay: 200">
                <?php foreach ($related_products as $related): 
                    $related_images = json_decode($related['product_pictures'], true);
if (!is_array($related_images)) {
    $related_images = [];
}
                ?>
                <div>
                    <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden product-card" data-product-id="<?php echo $related['product_id']; ?>">
                        <div class="uk-card-media-top uk-inline uk-light">
                            <img src="<?php echo htmlspecialchars(trim($related_images[0])); ?>" 
                                 alt="<?php echo htmlspecialchars($related['product_name']); ?>">
                            <div class="uk-position-cover uk-overlay-xlight"></div>
                            <div class="uk-position-small uk-position-top-left">
                                <span class="uk-label uk-text-bold uk-text-price">$<?php echo number_format($related['price'], 2); ?></span>
                            </div>
                        </div>
                        <div class="uk-card-body">
                            <h3 class="uk-card-title uk-margin-small-bottom">
                                <?php echo htmlspecialchars($related['product_name']); ?>
                            </h3>
                            <div class="uk-text-muted uk-text-small"><?php echo htmlspecialchars($related['username']); ?></div>
                        </div>
                        <a href="product.php?id=<?php echo $related['product_id']; ?>" class="uk-position-cover"></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?> 