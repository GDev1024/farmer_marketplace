<?php
class Config {
    // Database Configuration
    const DB_HOST = 'localhost';
    const DB_NAME = 'grenada_marketplace';
    const DB_USER = 'root';
    const DB_PASS = '';
    
    // AWS Configuration
    const AWS_REGION = 'us-east-1';
    const AWS_S3_BUCKET = 'grenada-farmer-marketplace';
    const AWS_ACCESS_KEY = 'YOUR_AWS_ACCESS_KEY';
    const AWS_SECRET_KEY = 'YOUR_AWS_SECRET_KEY';
    
    // Payment Configuration
    const STRIPE_PUBLIC_KEY = 'pk_test_YOUR_STRIPE_PUBLIC_KEY';
    const STRIPE_SECRET_KEY = 'sk_test_YOUR_STRIPE_SECRET_KEY';
    const PAYPAL_CLIENT_ID = 'YOUR_PAYPAL_CLIENT_ID';
    const PAYPAL_CLIENT_SECRET = 'YOUR_PAYPAL_CLIENT_SECRET';
    const PAYPAL_MODE = 'sandbox'; // 'sandbox' or 'live'
    
    // Application Settings
    const SITE_NAME = 'Grenada Farmer Marketplace';
    const SITE_URL = 'http://localhost/grenada-marketplace';
    const UPLOAD_PATH = 'uploads/';
    const MAX_FILE_SIZE = 5242880; // 5MB
    
    public static function getDB() {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}