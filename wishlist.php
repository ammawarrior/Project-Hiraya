<?php
require_once 'config/database.php';
include 'layout/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch liked products
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username, u.user_picture 
        FROM products p 
        JOIN users u ON p.user_id = u.user_id 
        JOIN product_likes pl ON p.product_id = pl.product_id 
        WHERE pl.user_id = :user_id 
        ORDER BY p.product_id DESC
    ");
    
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $liked_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching liked products: " . $e->getMessage());
}
?>

<div class="uk-section uk-margin-top">
    <div class="uk-container">
        <!-- Hero Section -->
        <div class="uk-position-relative uk-margin-large-bottom">
            <div class="uk-background-primary-dark uk-light uk-border-rounded-large uk-padding-large uk-box-shadow-small">
                <div class="uk-grid-medium" data-uk-grid>
                    <div class="uk-width-1-2@m">
                        <h1 class="uk-heading-medium uk-margin-remove">My Wishlist</h1>
                        <p class="uk-text-lead uk-margin-medium-top">Save and track your favorite innovations</p>
                    </div>
                    <div class="uk-width-1-2@m uk-flex uk-flex-middle uk-flex-right@m">
                        <span data-uk-icon="icon: heart; ratio: 4" class="uk-text-primary"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wishlist Items -->
        <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m" data-uk-grid>
            <?php if (empty($liked_products)): ?>
                <div class="uk-text-center uk-margin-large-top" style="display: none;">
                    <span data-uk-icon="icon: heart; ratio: 4" class="uk-text-muted"></span>
                    <h3 class="uk-margin-small-top">Your wishlist is empty</h3>
                    <p class="uk-text-muted">Start adding innovations to your wishlist to track them later</p>
                    <a href="products.php" class="uk-button uk-button-primary uk-margin-small-top">Browse Innovations</a>
                </div>
            <?php else: ?>
                <?php foreach ($liked_products as $product): 
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
                <div>
                    <div class="uk-card uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
                        <div class="uk-card-media-top uk-inline uk-light">
                            <img src="<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <div class="uk-position-cover uk-overlay-xlight"></div>
                            <div class="uk-position-small uk-position-top-right" style="z-index: 10;">
                                <a href="#"
                                   class="uk-icon-button uk-like liked"
                                   data-uk-icon="heart"
                                   data-product-id="<?php echo $product['product_id']; ?>">
                                </a>
                            </div>
                        </div>
                        <div class="uk-card-body">
                            <h3 class="uk-card-title uk-margin-small-bottom">
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </h3>
                            <div class="uk-text-muted uk-text-small">
                                <?php echo htmlspecialchars($product['username']); ?>
                            </div>
                            <div class="uk-text-muted uk-text-xxsmall uk-rating uk-margin-small-top">
                                <span class="uk-margin-small-left uk-text-bold">
                                    <?php echo $product['likes']; ?> likes
                                </span>
                            </div>
                            <div class="uk-margin-small-top uk-text-small uk-text-truncate">
                                <?php 
                                // Display a truncated version of the description
                                $description = strip_tags($product['product_description']);
                                echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '');
                                ?>
                            </div>
                        </div>
                        <div class="uk-card-footer">
                            <div class="uk-grid-small" data-uk-grid>
                                <div class="uk-width-1-2">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" class="uk-button uk-button-primary uk-width-1-1">View Details</a>
                                </div>
                                <div class="uk-width-1-2">
                                    <button class="uk-button uk-button-secondary uk-width-1-1">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.uk-like');

    likeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            
            // Check if user is logged in
            const checkLogin = function() {
                return fetch('check_login.php')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.logged_in) {
                            window.location.href = 'login.php';
                            return false;
                        }
                        return true;
                    })
                    .catch(error => {
                        console.error('Login check error:', error);
                        window.location.href = 'login.php';
                        return false;
                    });
            };

            checkLogin().then(isLoggedIn => {
                if (!isLoggedIn) return;

                const productId = this.getAttribute('data-product-id');

                fetch('like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update only the rating count display
                        const ratingCount = this.closest('.uk-card').querySelector('.uk-rating .uk-text-bold');
                        
                        if (ratingCount) {
                            const existingText = ratingCount.textContent;
                            const likesWord = existingText.includes('likes') ? ' likes' : '';
                            ratingCount.textContent = `${data.likes}${likesWord}`;
                        }
                        
                        // Toggle the liked class and update colors
                        this.classList.toggle('liked');
                        this.style.color = this.classList.contains('liked') ? 'red' : '';
                        
                        // Update SVG fill color
                        const svg = this.querySelector('svg');
                        if (svg) {
                            svg.style.fill = this.classList.contains('liked') ? 'red' : '';
                        }
                        
                        // Remove product from wishlist if unliked
                        if (!this.classList.contains('liked')) {
                            this.closest('div').remove();
                        }
                    } else if (data.error === 'login_required') {
                        window.location.href = 'login.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Like error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    });
});
</script>

<?php include 'layout/footer.php'; ?>
