<?php
require_once 'config/database.php';

try {
    // Check if product_likes table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_likes'");
    if ($stmt->rowCount() == 0) {
        echo "Error: product_likes table does not exist!\n";
        exit;
    }

    // Check table structure
    $stmt = $pdo->query("DESCRIBE product_likes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $required_columns = ['user_id', 'product_id'];
    $missing_columns = array_diff($required_columns, array_column($columns, 'Field'));
    
    if (!empty($missing_columns)) {
        echo "Error: Missing required columns in product_likes table: " . implode(', ', $missing_columns) . "\n";
        exit;
    }

    // Check if products table has likes column
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!in_array('likes', array_column($columns, 'Field'))) {
        echo "Error: products table is missing likes column!\n";
        exit;
    }

    echo "Database structure is correct.\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
