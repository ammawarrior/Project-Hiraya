<?php 
require_once 'config/database.php';
include 'layout/header.php';

// Only check for login when user tries to like a product
// The actual login check will be in the JavaScript for the like button

// Fetch products from database
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$query = "SELECT p.*, u.username, u.user_picture 
          FROM products p 
          JOIN users u ON p.user_id = u.user_id 
          WHERE p.status IN (1, 2)";

if ($category_filter) {
    $query .= " AND p.category = :category";
}

$stmt = $pdo->prepare($query);
if ($category_filter) {
    $stmt->bindValue(':category', $category_filter, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verify product_likes table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_likes'");
    if ($stmt->rowCount() == 0) {
        throw new Exception("product_likes table does not exist!");
    }
    
    // Verify table structure
    $stmt = $pdo->query("DESCRIBE product_likes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $required_columns = ['user_id', 'product_id'];
    $missing_columns = array_diff($required_columns, array_column($columns, 'Field'));
    
    if (!empty($missing_columns)) {
        throw new Exception("Missing required columns in product_likes table: " . implode(', ', $missing_columns));
    }
} catch (Exception $e) {
    // Debug output - remove in production
    // echo "Database error: " . $e->getMessage() . "<br>";
}

// Fetch products from database
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$query = "SELECT p.*, u.username, u.user_picture 
          FROM products p 
          JOIN users u ON p.user_id = u.user_id 
          WHERE p.status IN (1, 2)";

if ($category_filter) {
    $query .= " AND p.category = :category";
}

$stmt = $pdo->prepare($query);
if ($category_filter) {
    $stmt->bindValue(':category', $category_filter, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="uk-section uk-margin-top">
  <div class="uk-container">
    <!-- Hero Section -->
    <div class="uk-position-relative uk-margin-large-bottom">
      <div class="uk-background-primary-dark uk-light uk-border-rounded-large uk-padding-large uk-box-shadow-small">
        <div class="uk-grid-medium" data-uk-grid>
          <div class="uk-width-1-2@m">
            <h1 class="uk-heading-medium uk-margin-remove">Innovation Marketplace</h1>
            <p class="uk-text-lead uk-margin-medium-top">Discover groundbreaking technologies and innovative solutions</p>
          </div>
          <div class="uk-width-1-2@m uk-flex uk-flex-middle uk-flex-right@m">
            <span data-uk-icon="icon: future; ratio: 4" class="uk-text-primary"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="uk-card uk-card-default uk-card-hover uk-border-rounded-large uk-margin-bottom">
      <div class="uk-card-body">
        <div class="uk-grid-medium" data-uk-grid>
          <div class="uk-width-3-4@m">
            <div class="uk-inline uk-width-1-1">
              <span class="uk-form-icon"><span data-uk-icon="icon: search"></span></span>
              <input class="uk-input" type="text" placeholder="Search innovations...">
            </div>
          </div>
          <div class="uk-width-1-4@m">
            <select class="uk-select">
              <option>All Categories</option>
              <option>Agriculture</option>
              <option>Healthcare</option>
              <option>Energy</option>
              <option>Construction</option>
              <option>Product Design</option>
              <option>Information Technology</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l" data-uk-grid>
      <?php foreach ($products as $product): 

$user_liked = false;
if (isset($_SESSION['user_id'])) {
    try {
        // Debug output - remove in production
        // echo "Checking like status for User ID: " . $_SESSION['user_id'] . ", Product ID: " . $product['product_id'] . "<br>";
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM product_likes WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $_SESSION['user_id'], ':product_id' => $product['product_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_liked = $result['count'] > 0;
        
        // Debug output - remove in production
        // echo "Debug: Like count: " . $result['count'] . ", Liked: " . ($user_liked ? 'Yes' : 'No') . "<br>";
    } catch (PDOException $e) {
        // Debug output - remove in production
        // echo "Database error: " . $e->getMessage() . "<br>";
        $user_liked = false;
    }
}

        // Process product pictures (JSON-encoded array)
$product_images = json_decode($product['product_pictures'], true);
if (!is_array($product_images)) {
    $product_images = [];
}
    
// Get first image path
$first_image = !empty($product_images[0]) && file_exists($product_images[0]) 
    ? $product_images[0]
    : 'img/agriculture.jpg'; // Default image for products

// Remove quotes and slashes from JSON path
$first_image = str_replace(['\\\\', '\"'], ['', ''], $first_image);
        // Set default user picture if none is provided
        $user_picture = !empty($product['user_picture']) && file_exists("uploads/" . $product['user_picture'])
            ? "uploads/" . $product['user_picture']
            : 'img/profile_null.png';
      ?>
      <div>
        <div class="uk-card uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <img src="<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <div class="uk-position-cover uk-overlay-xlight"></div>
            <div class="uk-position-small uk-position-top-right" style="z-index: 10;">
    <a href="#"
       class="uk-icon-button uk-like <?php echo $user_liked ? 'liked' : ''; ?>"
       data-uk-icon="heart"
       data-product-id="<?php echo $product['product_id']; ?>">
    </a>
</div>

          </div>
          <div class="uk-card-body">
            <h3 class="uk-card-title uk-margin-small-bottom" data-category="<?php echo htmlspecialchars($product['category']); ?>">
              <?php echo htmlspecialchars($product['product_name']); ?>
            </h3>
            <div class="uk-text-muted uk-text-small"><?php echo htmlspecialchars($product['username']); ?></div>
            <div class="uk-text-muted uk-text-xxsmall uk-rating uk-margin-small-top">
              <span class="uk-margin-small-left uk-text-bold"><?php echo $product['likes']; ?> likes</span>
            </div>
            <div class="uk-margin-small-top uk-text-small uk-text-truncate">
              <?php 
              // Display a truncated version of the description
              $description = strip_tags($product['product_description']);
              echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '');
              ?>
            </div>
          </div>
          <a href="product.php?id=<?php echo $product['product_id']; ?>" class="uk-position-cover"></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>    

    <!-- Pagination -->
    <div class="uk-margin-large-top uk-text-center">
      <ul class="uk-pagination uk-flex-center" data-uk-margin>
        <li><a href="#"><span data-uk-pagination-previous></span></a></li>
        <li><a href="#">1</a></li>
        <li class="uk-active"><span>2</span></li>
        <li><a href="#">3</a></li>
        <li><a href="#"><span data-uk-pagination-next></span></a></li>
      </ul>
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
                            // Split the existing text to preserve "likes"
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

    /* Like Button Styles */
    .uk-icon-button.liked {
        color: red !important;
        fill: red !important;
    }
    
    .uk-icon-button.liked svg {
        fill: red !important;
        color: red !important;
    }
    
    .uk-icon-button.liked svg path {
        fill: red !important;
        stroke: red !important;
    }
    
    .uk-icon-button.liked svg circle {
        fill: red !important;
        stroke: red !important;
    }
</style>

<?php include 'layout/footer.php'; ?>


<!--   19 Nov 2019 03:39:33 GMT -->
</html>