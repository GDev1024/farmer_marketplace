<?php
require 'vendor/autoload.php'; // Composer autoload for AWS SDK

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AWSImageHandler {
    private $s3Client;
    private $bucket;
    private $useLocal = true;
    
    public function __construct() {
        $this->bucket = Config::AWS_S3_BUCKET;
        
        // Check if AWS credentials are configured
        if (Config::AWS_ACCESS_KEY !== 'YOUR_AWS_ACCESS_KEY') {
            try {
                $this->s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => Config::AWS_REGION,
                    'credentials' => [
                        'key' => Config::AWS_ACCESS_KEY,
                        'secret' => Config::AWS_SECRET_KEY,
                    ]
                ]);
                $this->useLocal = false;
            } catch (Exception $e) {
                error_log("AWS S3 initialization failed: " . $e->getMessage());
                $this->useLocal = true;
            }
        }
    }
    
    public function uploadImage($file, $folder = 'products') {
        $fileName = $this->generateFileName($file['name']);
        $filePath = $folder . '/' . $fileName;
        
        // Validate file
        if (!$this->validateImage($file)) {
            throw new Exception("Invalid image file");
        }
        
        // Process image (resize, optimize)
        $processedImage = $this->processImage($file['tmp_name']);
        
        if ($this->useLocal) {
            return $this->uploadLocal($processedImage, $filePath);
        } else {
            return $this->uploadToS3($processedImage, $filePath);
        }
    }
    
    private function uploadToS3($imagePath, $key) {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $imagePath,
                'ACL' => 'public-read',
                'ContentType' => mime_content_type($imagePath)
            ]);
            
            unlink($imagePath); // Clean up temp file
            return $result['ObjectURL'];
        } catch (AwsException $e) {
            error_log("S3 Upload Error: " . $e->getMessage());
            return $this->uploadLocal($imagePath, $key);
        }
    }
    
    private function uploadLocal($imagePath, $key) {
        $uploadDir = Config::UPLOAD_PATH . dirname($key);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $destination = Config::UPLOAD_PATH . $key;
        if (move_uploaded_file($imagePath, $destination)) {
            return Config::SITE_URL . '/' . $destination;
        }
        
        throw new Exception("Failed to upload image");
    }
    
    private function validateImage($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = Config::MAX_FILE_SIZE;
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        return true;
    }
    
    private function processImage($sourcePath) {
        $image = imagecreatefromstring(file_get_contents($sourcePath));
        
        // Resize to max 800x600
        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 800;
        $maxHeight = 600;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $newImage;
        }
        
        // Save to temp file
        $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
        imagejpeg($image, $tempPath, 85);
        imagedestroy($image);
        
        return $tempPath;
    }
    
    private function generateFileName($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    public function deleteImage($url) {
        if ($this->useLocal) {
            $path = str_replace(Config::SITE_URL . '/', '', $url);
            if (file_exists($path)) {
                unlink($path);
            }
        } else {
            $key = str_replace('https://' . $this->bucket . '.s3.amazonaws.com/', '', $url);
            try {
                $this->s3Client->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key
                ]);
            } catch (AwsException $e) {
                error_log("S3 Delete Error: " . $e->getMessage());
            }
        }
    }
}
