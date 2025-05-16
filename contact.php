<?php 
include 'config/database.php';
include 'layout/header.php';

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch seller's contact information
$stmt = $pdo->prepare("SELECT u.email, u.contact_number, u.username, u.user_picture 
                      FROM products p 
                      JOIN users u ON p.user_id = u.user_id 
                      WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle error case
if (!$seller) {
    echo "<div class='uk-alert-danger' data-uk-alert>
            <p>No seller found for this product.</p>
          </div>";
    include 'layout/footer.php';
    exit();
}

?>

<div class="uk-section uk-section-large" style="padding: 40px">
    <div class="uk-container uk-container-xsmall">
        <div class="uk-card uk-card-default uk-card-body uk-border-rounded-large uk-box-shadow-large uk-animation-fade">
            <div class="uk-text-center uk-margin-large-bottom">
                <h1 class="uk-heading-primary uk-text-bold uk-text-center">Connect with Our Innovators</h1>
                <p class="uk-text-large uk-text-lead uk-text-center uk-margin-large-bottom">
                    Be part of our community of innovators and technology providers. 
                    Connect with individuals and organizations driving positive change through innovative solutions.
                </p>
            </div>
            
            <div class="uk-text-center uk-margin-large-bottom">
                <?php 
                // Set default user picture if none is provided
                $user_picture = !empty($seller['user_picture']) && file_exists("uploads/" . $seller['user_picture'])
                    ? "uploads/" . $seller['user_picture']
                    : 'img/profile_null.png';
                ?>
                <img class="uk-border-circle uk-animation-slide-bottom-small uk-margin-bottom" 
                     width="150" height="150" 
                     src="<?php echo htmlspecialchars($user_picture); ?>" 
                     alt="<?php echo htmlspecialchars($seller['username']); ?>" 
                     style="border-radius: 50%; width: 150px; height: 150px; object-fit: cover;">
                <h2 class="uk-margin-remove-bottom uk-text-bold uk-animation-slide-right-small" style="margin-top: 20px; "><?php echo htmlspecialchars($seller['username']); ?></h2>
                <p class="uk-text-meta uk-margin-remove-top uk-animation-slide-right-small">Innovator</p>
            </div>

            <div class="uk-margin-large">
                <h3 class="uk-text-bold uk-margin-remove-bottom uk-animation-slide-left-small">Contact Details</h3>
                <div class="uk-text-break uk-margin-top uk-animation-slide-left-small">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($seller['contact_number']); ?></p>
                </div>
            </div>

            <div class="uk-margin-large">
                <a href="javascript:window.history.back();" 
                   class="uk-button uk-button-secondary uk-width-1-1 uk-box-shadow-medium uk-animation-scale-up">
                    Back to Product
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>