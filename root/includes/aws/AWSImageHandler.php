<?php
/**
 * AWS S3 Compatible Image Handler Class
 * Handles image upload to both local storage and AWS S3
 */
class AWSImageHandler {
    
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    private $imageWidth;
    private $imageHeight;
    private $thumbWidth;
    private $thumbHeight;
    private $useS3;
    private $s3Bucket;
    private $s3Region;
    private $s3AccessKey;
    private $s3SecretKey;
    private $cdnUrl;
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../uploads/products/';
        $this->allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        $this->imageWidth = 800;
        $this->imageHeight = 600;
        $this->thumbWidth = 200;
        $this->thumbHeight = 200;
        
        // AWS S3 Configuration (from environment variables)
        $this->useS3 = !empty($_ENV['AWS_S3_BUCKET']) || !empty(getenv('AWS_S3_BUCKET'));
        $this->s3Bucket = $_ENV['AWS_S3_BUCKET'] ?? getenv('AWS_S3_BUCKET') ?? '';
        $this->s3Region = $_ENV['AWS_S3_REGION'] ?? getenv('AWS_S3_REGION') ?? 'us-east-1';
        $this->s3AccessKey = $_ENV['AWS_ACCESS_KEY_ID'] ?? getenv('AWS_ACCESS_KEY_ID') ?? '';
        $this->s3SecretKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? getenv('AWS_SECRET_ACCESS_KEY') ?? '';
        $this->cdnUrl = $_ENV['AWS_CLOUDFRONT_URL'] ?? getenv('AWS_CLOUDFRONT_URL') ?? '';
        
        // Create local upload directory if it doesn't exist (fallback)
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload and process image (AWS S3 compatible)
     */
    public function uploadImage($file, $listingId) {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }
            
            // Generate unique filename
            $extension = $this->getFileExtension($file['name']);
            $filename = 'product_' . $listingId . '_' . time() . '.' . $extension;
            $thumbFilename = 'thumb_' . $filename;
            
            $tempImagePath = $this->uploadDir . 'temp_' . $filename;
            $tempThumbPath = $this->uploadDir . 'temp_' . $thumbFilename;
            
            // Move uploaded file to temp location
            if (!move_uploaded_file($file['tmp_name'], $tempImagePath)) {
                return ['success' => false, 'error' => 'Failed to upload file'];
            }
            
            // Resize main image
            $resizeResult = $this->resizeImage($tempImagePath, $this->imageWidth, $this->imageHeight);
            if (!$resizeResult) {
                unlink($tempImagePath);
                return ['success' => false, 'error' => 'Failed to resize image'];
            }
            
            // Create thumbnail
            $thumbResult = $this->createThumbnail($tempImagePath, $tempThumbPath, $this->thumbWidth, $this->thumbHeight);
            if (!$thumbResult) {
                unlink($tempImagePath);
                return ['success' => false, 'error' => 'Failed to create thumbnail'];
            }
            
            // Upload to S3 if configured, otherwise use local storage
            if ($this->useS3 && $this->isS3Configured()) {
                $s3Result = $this->uploadToS3($tempImagePath, $tempThumbPath, $filename, $thumbFilename);
                
                // Clean up temp files
                unlink($tempImagePath);
                unlink($tempThumbPath);
                
                if ($s3Result['success']) {
                    return [
                        'success' => true,
                        'image_path' => $s3Result['image_url'],
                        'thumbnail_path' => $s3Result['thumbnail_url'],
                        'storage' => 's3'
                    ];
                } else {
                    return ['success' => false, 'error' => 'S3 upload failed: ' . $s3Result['error']];
                }
            } else {
                // Use local storage
                $finalImagePath = $this->uploadDir . $filename;
                $finalThumbPath = $this->uploadDir . $thumbFilename;
                
                rename($tempImagePath, $finalImagePath);
                rename($tempThumbPath, $finalThumbPath);
                
                return [
                    'success' => true,
                    'image_path' => 'uploads/products/' . $filename,
                    'thumbnail_path' => 'uploads/products/' . $thumbFilename,
                    'storage' => 'local'
                ];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Upload files to AWS S3
     */
    private function uploadToS3($imagePath, $thumbPath, $filename, $thumbFilename) {
        try {
            // Simple S3 upload using cURL (for basic compatibility)
            // In production, use AWS SDK for PHP for better functionality
            
            $imageS3Key = 'products/' . $filename;
            $thumbS3Key = 'products/' . $thumbFilename;
            
            // Upload main image
            $imageUpload = $this->putS3Object($imagePath, $imageS3Key);
            if (!$imageUpload) {
                return ['success' => false, 'error' => 'Failed to upload main image to S3'];
            }
            
            // Upload thumbnail
            $thumbUpload = $this->putS3Object($thumbPath, $thumbS3Key);
            if (!$thumbUpload) {
                return ['success' => false, 'error' => 'Failed to upload thumbnail to S3'];
            }
            
            // Generate URLs
            $baseUrl = $this->cdnUrl ?: "https://{$this->s3Bucket}.s3.{$this->s3Region}.amazonaws.com";
            
            return [
                'success' => true,
                'image_url' => $baseUrl . '/' . $imageS3Key,
                'thumbnail_url' => $baseUrl . '/' . $thumbS3Key
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Simple S3 PUT operation using cURL
     */
    private function putS3Object($filePath, $s3Key) {
        $fileContent = file_get_contents($filePath);
        $contentType = mime_content_type($filePath);
        
        $host = "{$this->s3Bucket}.s3.{$this->s3Region}.amazonaws.com";
        $url = "https://{$host}/{$s3Key}";
        
        $date = gmdate('D, d M Y H:i:s T');
        $stringToSign = "PUT\n\n{$contentType}\n{$date}\n/{$this->s3Bucket}/{$s3Key}";
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->s3SecretKey, true));
        
        $headers = [
            "Host: {$host}",
            "Date: {$date}",
            "Content-Type: {$contentType}",
            "Authorization: AWS {$this->s3AccessKey}:{$signature}"
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
    
    /**
     * Check if S3 is properly configured
     */
    private function isS3Configured() {
        return !empty($this->s3Bucket) && !empty($this->s3AccessKey) && !empty($this->s3SecretKey);
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'File upload error'];
        }
        
        if ($file['size'] > $this->maxFileSize) {
            return ['valid' => false, 'error' => 'File too large (max 5MB)'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type. Only JPG and PNG allowed'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Get file extension from filename
     */
    private function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
    
    /**
     * Resize image to specified dimensions
     */
    private function resizeImage($imagePath, $newWidth, $newHeight) {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) return false;
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];
        
        // Calculate aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;
        
        if ($newWidth / $newHeight > $aspectRatio) {
            $newWidth = $newHeight * $aspectRatio;
        } else {
            $newHeight = $newWidth / $aspectRatio;
        }
        
        // Create image resource based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) return false;
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($imageType == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        // Resize
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Save resized image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($newImage, $imagePath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($newImage, $imagePath, 8);
                break;
            default:
                $result = false;
        }
        
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return $result;
    }
    
    /**
     * Create thumbnail image
     */
    private function createThumbnail($sourcePath, $thumbPath, $thumbWidth, $thumbHeight) {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];
        
        // Create source image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) return false;
        
        // Calculate crop dimensions for square thumbnail
        $cropSize = min($originalWidth, $originalHeight);
        $cropX = ($originalWidth - $cropSize) / 2;
        $cropY = ($originalHeight - $cropSize) / 2;
        
        // Create thumbnail
        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // Preserve transparency for PNG
        if ($imageType == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
        
        // Crop and resize
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, $cropX, $cropY, $thumbWidth, $thumbHeight, $cropSize, $cropSize);
        
        // Save thumbnail
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($thumbnail, $thumbPath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($thumbnail, $thumbPath, 8);
                break;
            default:
                $result = false;
        }
        
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
        
        return $result;
    }
    
    /**
     * Delete image files (S3 compatible)
     */
    public function deleteImage($imagePath, $thumbnailPath = null) {
        $deleted = true;
        
        if ($this->useS3 && $this->isS3Configured()) {
            // Delete from S3
            if ($imagePath) {
                $deleted = $this->deleteS3Object($imagePath) && $deleted;
            }
            if ($thumbnailPath) {
                $deleted = $this->deleteS3Object($thumbnailPath) && $deleted;
            }
        } else {
            // Delete from local storage
            if ($imagePath && file_exists($this->uploadDir . basename($imagePath))) {
                $deleted = unlink($this->uploadDir . basename($imagePath)) && $deleted;
            }
            if ($thumbnailPath && file_exists($this->uploadDir . basename($thumbnailPath))) {
                $deleted = unlink($this->uploadDir . basename($thumbnailPath)) && $deleted;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Delete object from S3
     */
    private function deleteS3Object($url) {
        // Extract S3 key from URL
        $parsedUrl = parse_url($url);
        $s3Key = ltrim($parsedUrl['path'], '/');
        
        $host = "{$this->s3Bucket}.s3.{$this->s3Region}.amazonaws.com";
        $deleteUrl = "https://{$host}/{$s3Key}";
        
        $date = gmdate('D, d M Y H:i:s T');
        $stringToSign = "DELETE\n\n\n{$date}\n/{$this->s3Bucket}/{$s3Key}";
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->s3SecretKey, true));
        
        $headers = [
            "Host: {$host}",
            "Date: {$date}",
            "Authorization: AWS {$this->s3AccessKey}:{$signature}"
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $deleteUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 204;
    }
    
    /**
     * Get storage info for debugging
     */
    public function getStorageInfo() {
        return [
            'using_s3' => $this->useS3 && $this->isS3Configured(),
            's3_bucket' => $this->s3Bucket,
            's3_region' => $this->s3Region,
            'cdn_url' => $this->cdnUrl,
            'local_dir' => $this->uploadDir
        ];
    }
}
?>