<?php
require_once __DIR__ . '/env-loader.php';

// Load environment variables
EnvLoader::load();

class Config {
    // Database Configuration
    public static function getDbHost() {
        return EnvLoader::get('DB_HOST', 'localhost');
    }
    
    public static function getDbName() {
        return EnvLoader::get('DB_NAME', 'grenada_marketplace');
    }
    
    public static function getDbUser() {
        return EnvLoader::get('DB_USER', 'root');
    }
    
    public static function getDbPass() {
        return EnvLoader::get('DB_PASS', '');
    }
    
    // AWS Configuration
    public static function getAwsRegion() {
        return EnvLoader::get('AWS_REGION', 'us-east-1');
    }
    
    public static function getAwsS3Bucket() {
        return EnvLoader::get('AWS_S3_BUCKET', 'grenada-farmer-marketplace');
    }
    
    public static function getAwsAccessKey() {
        return EnvLoader::get('AWS_ACCESS_KEY');
    }
    
    public static function getAwsSecretKey() {
        return EnvLoader::get('AWS_SECRET_KEY');
    }
    
    // Payment Configuration
    public static function getStripePublicKey() {
        return EnvLoader::get('STRIPE_PUBLISHABLE_KEY');
    }
    
    public static function getStripeSecretKey() {
        return EnvLoader::get('STRIPE_SECRET_KEY');
    }
    
    public static function getPaypalClientId() {
        return EnvLoader::get('PAYPAL_CLIENT_ID');
    }
    
    public static function getPaypalClientSecret() {
        return EnvLoader::get('PAYPAL_CLIENT_SECRET');
    }
    
    public static function getPaypalMode() {
        return EnvLoader::get('PAYPAL_MODE', 'sandbox');
    }
    
    // Application Settings
    public static function getSiteName() {
        return EnvLoader::get('SITE_NAME', 'Grenada Farmer Marketplace');
    }
    
    public static function getSiteUrl() {
        return EnvLoader::get('SITE_URL', 'http://localhost/grenada-marketplace');
    }
    
    public static function getUploadPath() {
        return EnvLoader::get('UPLOAD_PATH', 'uploads/');
    }
    
    public static function getMaxFileSize() {
        return (int) EnvLoader::get('MAX_FILE_SIZE', 5242880);
    }
    
    // Security Settings
    public static function getSessionLifetime() {
        return (int) EnvLoader::get('SESSION_LIFETIME', 3600);
    }
    
    public static function getBcryptCost() {
        return (int) EnvLoader::get('BCRYPT_COST', 12);
    }
    
    // Backward compatibility constants
    const SITE_NAME = 'Grenada Farmer Marketplace';
    const STRIPE_PUBLIC_KEY = ''; // Will be overridden by env
    const PAYPAL_CLIENT_ID = ''; // Will be overridden by env
    
    public static function getDB() {
        try {
            $dsn = "mysql:host=" . self::getDbHost() . ";dbname=" . self::getDbName() . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, self::getDbUser(), self::getDbPass(), $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    // Environment detection
    public static function isProduction() {
        return EnvLoader::get('APP_ENV', 'development') === 'production';
    }
    
    public static function isDevelopment() {
        return !self::isProduction();
    }
}