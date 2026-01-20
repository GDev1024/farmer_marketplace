<?php
/**
 * Image Handler Class
 * Handles image upload, validation, resizing, and thumbnail generation
 */
class ImageHandler {
    
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    private $imageWidth;
    private $imageHeight;
    private $thumbWidth;
    private $thumbHeight;
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../uploads/products/';
        $this->allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        $this->imageWidth = 800;
        $this->imageHeight = 600;
        $this->thumbWidth = 200;
        $this->thumbHeight = 200;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload and process image
     * @param array $file $_FILES array element
     * @param int $listingId Listing ID for unique naming
     * @return array Result with success status and file paths or error message
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
            
            $imagePath = $this->uploadDir . $filename;
            $thumbPath = $this->uploadDir . $thumbFilename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $imagePath)) {
                return ['success' => false, 'error' => 'Failed to upload file'];
            }
            
            // Resize main image
            $resizeResult = $this->resizeImage($imagePath, $this->imageWidth, $this->imageHeight);
            if (!$resizeResult) {
                unlink($imagePath);
                return ['success' => false, 'error' => 'Failed to resize image'];
            }
            
            // Create thumbnail
            $thumbResult = $this->createThumbnail($imagePath, $thumbPath, $this->thumbWidth, $this->thumbHeight);
            if (!$thumbResult) {
                unlink($imagePath);
                return ['success' => false, 'error' => 'Failed to create thumbnail'];
            }
            
            return [
                'success' => true,
                'image_path' => 'uploads/products/' . $filename,
                'thumbnail_path' => 'uploads/products/' . $thumbFilename
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
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
     * Delete image files
     */
    public function deleteImage($imagePath, $thumbnailPath = null) {
        $deleted = true;
        
        if ($imagePath && file_exists($this->uploadDir . basename($imagePath))) {
            $deleted = unlink($this->uploadDir . basename($imagePath)) && $deleted;
        }
        
        if ($thumbnailPath && file_exists($this->uploadDir . basename($thumbnailPath))) {
            $deleted = unlink($this->uploadDir . basename($thumbnailPath)) && $deleted;
        }
        
        return $deleted;
    }
}
?>