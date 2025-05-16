<?php
require_once 'config/database.php';
session_start();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to like a product.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

try {
    // Check if the user has already liked the product
    $stmt = $pdo->prepare("SELECT * FROM product_likes WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);

    if ($stmt->rowCount() > 0) {
        // If liked, remove the like
        $stmt = $pdo->prepare("DELETE FROM product_likes WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);

        // Decrease like count
        $stmt = $pdo->prepare("UPDATE products SET likes = likes - 1 WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $product_id]);

        $response['message'] = 'Like removed';
    } else {
        // If not liked, add the like
        $stmt = $pdo->prepare("INSERT INTO product_likes (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);

        // Increase like count
        $stmt = $pdo->prepare("UPDATE products SET likes = likes + 1 WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $product_id]);

        $response['message'] = 'Product liked';
    }

    // Fetch the updated like count
    $stmt = $pdo->prepare("SELECT likes FROM products WHERE product_id = :product_id");
    $stmt->execute([':product_id' => $product_id]);
    $likes = $stmt->fetchColumn();

    $response['success'] = true;
    $response['likes'] = $likes;
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
