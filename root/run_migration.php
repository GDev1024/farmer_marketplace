<?php
// Simple migration runner
require_once 'includes/Config.php';
Config::load();

// Database Connection
$dbConfig = Config::getDatabase();
$host = $dbConfig['host'];
$db = $dbConfig['name'];
$user = $dbConfig['user'];
$pass = $dbConfig['pass'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create migrations table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration_name VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // List of migrations to run
    $migrations = [
        'v2.0_add_images' => [
            "ALTER TABLE listings ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) NULL AFTER description",
            "ALTER TABLE listings ADD COLUMN IF NOT EXISTS thumbnail_path VARCHAR(255) NULL AFTER image_path",
            "CREATE INDEX IF NOT EXISTS idx_listings_has_image ON listings(image_path)"
        ],
        'v2.1_add_payment_tracking' => [
            "ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending' AFTER status",
            "ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL AFTER payment_status",
            "ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_transaction_id VARCHAR(255) NULL AFTER payment_method",
            "ALTER TABLE orders ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            "ALTER TABLE orders ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            "ALTER TABLE order_items ADD COLUMN IF NOT EXISTS price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER quantity",
            "UPDATE order_items oi JOIN listings l ON oi.listing_id = l.id SET oi.price = l.price WHERE oi.price = 0.00"
        ]
    ];
    
    foreach ($migrations as $migrationName => $queries) {
        // Check if migration already ran
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration_name = ?");
        $stmt->execute([$migrationName]);
        
        if ($stmt->fetchColumn() > 0) {
            echo "Migration $migrationName already executed, skipping...\n";
            continue;
        }
        
        echo "Running migration $migrationName...\n";
        
        try {
            $pdo->beginTransaction();
            
            foreach ($queries as $query) {
                $pdo->exec($query);
                echo "  - Executed: " . substr($query, 0, 50) . "...\n";
            }
            
            // Mark migration as completed
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
            $stmt->execute([$migrationName]);
            
            $pdo->commit();
            echo "Migration $migrationName completed successfully!\n\n";
            
        } catch (PDOException $e) {
            $pdo->rollback();
            echo "Migration $migrationName failed: " . $e->getMessage() . "\n";
            break;
        }
    }
    
    echo "All migrations completed!\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>