<?php 
require_once 'config/database.php';
include 'layout/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
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
    <div class="uk-grid-small uk-flex uk-flex-middle" data-uk-grid>
      <div class="uk-width-expand@m">
        <h2>Products</h2>
      </div>
    </div>
    <div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-match uk-margin-medium-top" data-uk-grid>
      <?php foreach ($products as $product): 

$user_liked = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT 1 FROM product_likes WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([':user_id' => $_SESSION['user_id'], ':product_id' => $product['product_id']]);
    $user_liked = $stmt->fetchColumn() ? true : false;
}

        // Process product pictures (assuming they are stored as comma-separated paths)
        $product_images = explode(',', $product['product_pictures']);
        $first_image = !empty(trim($product_images[0])) && file_exists("uploads/" . trim($product_images[0])) 
            ? "uploads/" . trim($product_images[0])
            : 'img/agriculture.jpg'; // Default image for products

        // Set default user picture if none is provided
        $user_picture = !empty($product['user_picture']) && file_exists("uploads/" . $product['user_picture'])
            ? "uploads/" . $product['user_picture']
            : 'img/profile_null.png';
      ?>
      <div>
        <div class="uk-card uk-card-small uk-card-default uk-card-hover uk-border-rounded-large uk-overflow-hidden">
          <div class="uk-card-media-top uk-inline uk-light">
            <img src="<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <div class="uk-position-cover uk-overlay-xlight"></div>
            <div class="uk-position-small uk-position-top-right" style="z-index: 10;">
    <a href="#"
       class="uk-icon-button uk-like"
       data-uk-icon="heart"
       data-product-id="<?php echo $product['product_id']; ?>"
       style="<?php echo $user_liked ? 'color: red;' : ''; ?>">
    </a>
    <span><?php echo $product['likes']; ?> likes</span>
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
  </div>
</div>

<div id="offcanvas" data-uk-offcanvas="flip: true; overlay: true">
  <div class="uk-offcanvas-bar">
    <a class="uk-logo" href="index.php">Hiraya</a>
    <button class="uk-offcanvas-close" type="button" data-uk-close="ratio: 1.2"></button>
    <ul class="uk-nav uk-nav-primary uk-nav-offcanvas uk-margin-medium-top uk-text-center">
      <li class="uk-active"><a href="index.php">Courses</a></li>
      <li ><a href="events.html">Events</a></li>
      <li ><a href="course.html">Course</a></li>
      <li ><a href="event.html">Event</a></li>
      <li ><a href="search.html">Search</a></li>
      <li ><a href="sign-in.html">Sign In</a></li>
      <li ><a href="sign-up.html">Sign Up</a></li>
    </ul>
    <div class="uk-margin-medium-top">
      <a class="uk-button uk-width-1-1 uk-button-primary-light" href="sign-up.html">Sign Up</a>
    </div>
    <div class="uk-margin-medium-top uk-text-center">
      <div data-uk-grid class="uk-child-width-auto uk-grid-small uk-flex-center">
        <div>
          <a href="https://twitter.com/" data-uk-icon="icon: twitter" class="uk-icon-link" target="_blank"></a>
        </div>
        <div>
          <a href="https://www.facebook.com/" data-uk-icon="icon: facebook" class="uk-icon-link" target="_blank"></a>
        </div>
        <div>
          <a href="https://www.instagram.com/" data-uk-icon="icon: instagram" class="uk-icon-link" target="_blank"></a>
        </div>
        <div>
          <a href="https://vimeo.com/" data-uk-icon="icon: vimeo" class="uk-icon-link" target="_blank"></a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.uk-like');

    likeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
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
                    const likesCount = this.nextElementSibling;
                    likesCount.textContent = `${data.likes} likes`;
                    this.classList.toggle('liked');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>


<?php include 'layout/footer.php'; ?>


<!--   19 Nov 2019 03:39:33 GMT -->
</html>