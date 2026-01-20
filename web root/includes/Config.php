<?php
/**
 * Configuration Loader for AWS and Environment Variables
 */
class Config {
    private static $loaded = false;
    
    /**
     * Load environment variables from .env file
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }
        
        $envFile = __DIR__ . '/../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue; // Skip comments
                }
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (preg_match('/^"(.*)"$/', $value, $matches)) {
                        $value = $matches[1];
                    } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                        $value = $matches[1];
                    }
                    
                    // Set environment variable if not already set
                    if (!getenv($key)) {
                        putenv("$key=$value");
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Get configuration value
     */
    public static function get($key, $default = null) {
        self::load();
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
    
    /**
     * Get database configuration
     */
    public static function getDatabase() {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'name' => self::get('DB_NAME', 'grenada_farmers'),
            'user' => self::get('DB_USER', 'root'),
            'pass' => self::get('DB_PASS', '')
        ];
    }
    
    /**
     * Check if running on AWS
     */
    public static function isAWS() {
        // Check for AWS environment indicators
        return !empty(self::get('AWS_S3_BUCKET')) || 
               !empty($_SERVER['AWS_REGION']) ||
               file_exists('/opt/elasticbeanstalk/') ||
               !empty($_SERVER['AWS_EXECUTION_ENV']);
    }
    
    /**
     * Get AWS configuration
     */
    public static function getAWS() {
        return [
            's3_bucket' => self::get('AWS_S3_BUCKET'),
            's3_region' => self::get('AWS_S3_REGION', 'us-east-1'),
            'access_key' => self::get('AWS_ACCESS_KEY_ID'),
            'secret_key' => self::get('AWS_SECRET_ACCESS_KEY'),
            'cloudfront_url' => self::get('AWS_CLOUDFRONT_URL')
        ];
    }
}
?>