<?php
// Simple migration runner
$host = 'localhost';
$db = 'grenada_farmers';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Running migration v2.0_add_images.sql...\n";
    
    // Add image columns
    $pdo->exec("ALTER TABLE listings ADD COLUMN image_path VARCHAR(255) NULL AFTER image_url");
    echo "Added image_path column\n";
    
    $pdo->exec("ALTER TABLE listings ADD COLUMN thumbnail_path VARCHAR(255) NULL AFTER image_path");
    echo "Added thumbnail_path column\n";
    
    // Add index
    $pdo->exec("CREATE INDEX idx_listings_has_image ON listings(image_path)");
    echo "Added index for image queries\n";
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>