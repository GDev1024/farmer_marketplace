<?php
/**
 * Asset Optimizer
 * Optimizes images and assets for better performance
 */

class AssetOptimizer {
    
    private $uploadsDir;
    private $optimizedDir;
    
    public function __construct() {
        $this->uploadsDir = __DIR__ . '/../uploads/';
        $this->optimizedDir = __DIR__ . '/../uploads/optimized/';
        
        // Create optimized directory if it doesn't exist
        if (!is_dir($this->optimizedDir)) {
            mkdir($this->optimizedDir, 0755, true);
        }
    }
    
    /**
     * Optimize image for web display
     */
    public function optimizeImage($imagePath, $maxWidth = 800, $quality = 85) {
        if (!file_exists($imagePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Skip optimization if image is already small enough
        if ($originalWidth <= $maxWidth) {
            return $imagePath;
        }
        
        // Calculate new dimensions
        $newWidth = $maxWidth;
        $newHeight = intval(($originalHeight * $maxWidth) / $originalWidth);
        
        // Create optimized filename
        $pathInfo = pathinfo($imagePath);
        $optimizedPath = $this->optimizedDir . $pathInfo['filename'] . '_optimized.' . $pathInfo['extension'];
        
        // Check if optimized version already exists and is newer
        if (file_exists($optimizedPath) && filemtime($optimizedPath) > filemtime($imagePath)) {
            return $optimizedPath;
        }
        
        // Create image resource based on type
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($imagePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($imagePath);
                }
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) {
            return false;
        }
        
        // Create new image
        $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
            imagefilledrectangle($optimizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled(
            $optimizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        
        // Save optimized image
        $success = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $success = imagejpeg($optimizedImage, $optimizedPath, $quality);
                break;
            case 'image/png':
                $success = imagepng($optimizedImage, $optimizedPath, 9);
                break;
            case 'image/gif':
                $success = imagegif($optimizedImage, $optimizedPath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $success = imagewebp($optimizedImage, $optimizedPath, $quality);
                }
                break;
        }
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($optimizedImage);
        
        return $success ? $optimizedPath : false;
    }
    
    /**
     * Generate responsive image sizes
     */
    public function generateResponsiveImages($imagePath, $sizes = [400, 800, 1200]) {
        $responsiveImages = [];
        
        foreach ($sizes as $size) {
            $optimizedPath = $this->optimizeImage($imagePath, $size);
            if ($optimizedPath) {
                $responsiveImages[$size] = $optimizedPath;
            }
        }
        
        return $responsiveImages;
    }
    
    /**
     * Generate WebP version of image
     */
    public function generateWebP($imagePath, $quality = 85) {
        if (!function_exists('imagewebp')) {
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }
        
        $mimeType = $imageInfo['mime'];
        $pathInfo = pathinfo($imagePath);
        $webpPath = $this->optimizedDir . $pathInfo['filename'] . '.webp';
        
        // Check if WebP version already exists and is newer
        if (file_exists($webpPath) && filemtime($webpPath) > filemtime($imagePath)) {
            return $webpPath;
        }
        
        // Create source image
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($imagePath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) {
            return false;
        }
        
        // Convert to WebP
        $success = imagewebp($sourceImage, $webpPath, $quality);
        imagedestroy($sourceImage);
        
        return $success ? $webpPath : false;
    }
    
    /**
     * Get optimized image URL
     */
    public function getOptimizedImageUrl($imagePath, $size = 800) {
        $optimizedPath = $this->optimizeImage($imagePath, $size);
        
        if ($optimizedPath) {
            // Convert file path to URL
            $relativePath = str_replace(__DIR__ . '/../', '', $optimizedPath);
            return $relativePath;
        }
        
        return $imagePath;
    }
    
    /**
     * Generate picture element with responsive images
     */
    public function generatePictureElement($imagePath, $alt = '', $sizes = [400, 800, 1200], $class = '') {
        $responsiveImages = $this->generateResponsiveImages($imagePath, $sizes);
        $webpPath = $this->generateWebP($imagePath);
        
        $html = '<picture' . ($class ? " class=\"{$class}\"" : '') . '>';
        
        // Add WebP sources if available
        if ($webpPath) {
            $webpSizes = [];
            foreach ($sizes as $size) {
                $webpSizePath = str_replace('.webp', "_{$size}.webp", $webpPath);
                if (file_exists($webpSizePath)) {
                    $webpSizes[] = $webpSizePath . " {$size}w";
                }
            }
            
            if (!empty($webpSizes)) {
                $html .= '<source srcset="' . implode(', ', $webpSizes) . '" type="image/webp">';
            }
        }
        
        // Add regular image sources
        $srcset = [];
        foreach ($responsiveImages as $size => $path) {
            $url = str_replace(__DIR__ . '/../', '', $path);
            $srcset[] = $url . " {$size}w";
        }
        
        if (!empty($srcset)) {
            $html .= '<source srcset="' . implode(', ', $srcset) . '">';
        }
        
        // Fallback image
        $fallbackUrl = str_replace(__DIR__ . '/../', '', $imagePath);
        $html .= "<img src=\"{$fallbackUrl}\" alt=\"{$alt}\" loading=\"lazy\">";
        
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Clean up old optimized images
     */
    public function cleanupOptimizedImages($maxAge = 2592000) { // 30 days
        $files = glob($this->optimizedDir . '*');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Get image optimization statistics
     */
    public function getOptimizationStats() {
        $originalSize = 0;
        $optimizedSize = 0;
        $originalCount = 0;
        $optimizedCount = 0;
        
        // Scan uploads directory
        $uploadFiles = glob($this->uploadsDir . '**/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        foreach ($uploadFiles as $file) {
            if (is_file($file)) {
                $originalSize += filesize($file);
                $originalCount++;
            }
        }
        
        // Scan optimized directory
        $optimizedFiles = glob($this->optimizedDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        foreach ($optimizedFiles as $file) {
            if (is_file($file)) {
                $optimizedSize += filesize($file);
                $optimizedCount++;
            }
        }
        
        $savings = $originalSize > 0 ? (($originalSize - $optimizedSize) / $originalSize) * 100 : 0;
        
        return [
            'original_size' => $originalSize,
            'optimized_size' => $optimizedSize,
            'original_count' => $originalCount,
            'optimized_count' => $optimizedCount,
            'savings_percentage' => round($savings, 2),
            'savings_bytes' => $originalSize - $optimizedSize
        ];
    }
    
    /**
     * Generate lazy loading image HTML with multiple optimizations
     */
    public static function optimizedLazyImage($src, $alt = '', $class = '', $sizes = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw') {
        $optimizer = new AssetOptimizer();
        
        // Generate responsive images
        $responsiveImages = $optimizer->generateResponsiveImages($src, [400, 800, 1200]);
        $webpPath = $optimizer->generateWebP($src);
        
        $classAttr = $class ? " class=\"{$class}\"" : '';
        $placeholder = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+';
        
        $html = '<picture' . $classAttr . '>';
        
        // Add WebP sources if available
        if ($webpPath && !empty($responsiveImages)) {
            $webpSrcset = [];
            foreach ($responsiveImages as $size => $path) {
                $webpSizePath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
                if (file_exists($webpSizePath)) {
                    $webpUrl = str_replace(__DIR__ . '/../', '', $webpSizePath);
                    $webpSrcset[] = $webpUrl . " {$size}w";
                }
            }
            
            if (!empty($webpSrcset)) {
                $html .= '<source srcset="' . implode(', ', $webpSrcset) . '" sizes="' . $sizes . '" type="image/webp">';
            }
        }
        
        // Add regular image sources
        if (!empty($responsiveImages)) {
            $srcset = [];
            foreach ($responsiveImages as $size => $path) {
                $url = str_replace(__DIR__ . '/../', '', $path);
                $srcset[] = $url . " {$size}w";
            }
            
            if (!empty($srcset)) {
                $html .= '<source srcset="' . implode(', ', $srcset) . '" sizes="' . $sizes . '">';
            }
        }
        
        // Fallback image with lazy loading
        $fallbackUrl = str_replace(__DIR__ . '/../', '', $src);
        $html .= "<img src=\"{$placeholder}\" data-src=\"{$fallbackUrl}\" alt=\"{$alt}\" loading=\"lazy\" decoding=\"async\">";
        
        $html .= '</picture>';
        
        return $html;
    }
    
    /**
     * Optimize images in a directory recursively
     */
    public function optimizeDirectory($directory, $maxWidth = 800, $quality = 85) {
        $optimizedCount = 0;
        $totalSavings = 0;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $originalSize = $file->getSize();
                    $optimizedPath = $this->optimizeImage($file->getPathname(), $maxWidth, $quality);
                    
                    if ($optimizedPath && file_exists($optimizedPath)) {
                        $optimizedSize = filesize($optimizedPath);
                        $savings = $originalSize - $optimizedSize;
                        
                        if ($savings > 0) {
                            $totalSavings += $savings;
                            $optimizedCount++;
                        }
                    }
                }
            }
        }
        
        return [
            'optimized_count' => $optimizedCount,
            'total_savings' => $totalSavings,
            'total_savings_formatted' => $this->formatBytes($totalSavings)
        ];
    }
    
    /**
     * Generate critical image preloads
     */
    public function generateCriticalImagePreloads($images) {
        $preloads = [];
        
        foreach ($images as $image) {
            $optimizedPath = $this->optimizeImage($image['src'], 800, 85);
            $webpPath = $this->generateWebP($image['src']);
            
            if ($webpPath) {
                $webpUrl = str_replace(__DIR__ . '/../', '', $webpPath);
                $preloads[] = "<link rel=\"preload\" as=\"image\" href=\"{$webpUrl}\" type=\"image/webp\">";
            } elseif ($optimizedPath) {
                $optimizedUrl = str_replace(__DIR__ . '/../', '', $optimizedPath);
                $preloads[] = "<link rel=\"preload\" as=\"image\" href=\"{$optimizedUrl}\">";
            }
        }
        
        return implode("\n", $preloads);
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Generate JavaScript for lazy loading
     */
    public static function getLazyLoadingScript() {
        return "
        <script>
        // Lazy Loading Implementation
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                lazyImages.forEach(img => {
                    img.classList.add('lazy');
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for browsers without IntersectionObserver
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                });
            }
        });
        </script>";
    }
}