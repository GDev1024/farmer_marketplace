<?php
/**
 * Test Polish Features
 * Demonstrates enhanced loading states, empty states, and optimized assets
 */

require_once 'includes/polish-integration.php';

$polish = new PolishIntegration();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polish Features Test - Grenada Farmer Marketplace</title>
    
    <!-- Critical CSS -->
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/loading-states.css">
    
    <style>
        .test-section {
            margin: 2rem 0;
            padding: 2rem;
            border: 1px solid var(--border-secondary);
            border-radius: var(--radius-lg);
        }
        
        .test-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }
        
        .demo-container {
            min-height: 300px;
            border: 2px dashed var(--border-secondary);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .keyboard-navigation .btn:focus,
        .keyboard-navigation button:focus,
        .keyboard-navigation a:focus {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="page">
    <?php $polish->initializePage('test'); ?>
    
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="nav-brand">
                    <span>🌾</span>
                    <span>Grenada Farmer Marketplace</span>
                </a>
            </nav>
        </div>
    </header>
    
    <main class="page-main" id="main-content">
        <div class="container">
            <h1>Polish Features Test Page</h1>
            <p>This page demonstrates the enhanced loading states, empty states, and asset optimization features.</p>
            
            <!-- Loading States Test -->
            <section class="test-section">
                <h2>Loading States</h2>
                <p>Test various loading states for different scenarios:</p>
                
                <div class="test-buttons">
                    <button class="btn btn-primary" onclick="testFormLoading()">Test Form Loading</button>
                    <button class="btn btn-primary" onclick="testAsyncLoading()">Test Async Operation</button>
                    <button class="btn btn-primary" onclick="testPaymentLoading()">Test Payment Processing</button>
                    <button class="btn btn-primary" onclick="testUploadLoading()">Test File Upload</button>
                    <button class="btn btn-secondary" data-loading="Saving changes">Button with Loading</button>
                </div>
                
                <div id="loading-demo" class="demo-container">
                    <p>Loading states will appear here</p>
                </div>
            </section>
            
            <!-- Empty States Test -->
            <section class="test-section">
                <h2>Empty States</h2>
                <p>Test various empty states for different scenarios:</p>
                
                <div class="test-buttons">
                    <button class="btn btn-primary" onclick="testEmptyCart()">Empty Cart</button>
                    <button class="btn btn-primary" onclick="testNoProducts()">No Products</button>
                    <button class="btn btn-primary" onclick="testNoSearchResults()">No Search Results</button>
                    <button class="btn btn-primary" onclick="testConnectionError()">Connection Error</button>
                    <button class="btn btn-primary" onclick="testMaintenanceMode()">Maintenance Mode</button>
                    <button class="btn btn-primary" onclick="testOfflineMode()">Offline Mode</button>
                </div>
                
                <div id="empty-demo" class="demo-container">
                    <p>Empty states will appear here</p>
                </div>
            </section>
            
            <!-- Image Optimization Test -->
            <section class="test-section">
                <h2>Optimized Images</h2>
                <p>Test optimized image loading with lazy loading and responsive images:</p>
                
                <div class="test-buttons">
                    <button class="btn btn-primary" onclick="loadOptimizedImages()">Load Optimized Images</button>
                    <button class="btn btn-secondary" onclick="clearImages()">Clear Images</button>
                </div>
                
                <div id="image-demo" class="demo-container">
                    <p>Optimized images will appear here</p>
                </div>
            </section>
            
            <!-- Form Test -->
            <section class="test-section">
                <h2>Form with Loading States</h2>
                <form id="test-form" data-operation="Processing form">
                    <div class="form-group">
                        <label for="test-email" class="form-label">Email</label>
                        <input type="email" id="test-email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="test-message" class="form-label">Message</label>
                        <textarea id="test-message" name="message" class="form-textarea" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" data-async="Submitting form">
                        Submit Form
                    </button>
                </form>
            </section>
        </div>
    </main>
    
    <!-- Scripts -->
    <script src="assets/loading-enhancements.js"></script>
    <script src="assets/main.js"></script>
    <?php echo $polish->getEnhancementScript(); ?>
    
    <script>
        // Test functions for loading states
        function testFormLoading() {
            const demo = document.getElementById('loading-demo');
            demo.innerHTML = <?php echo json_encode(LoadingStates::formLoading()); ?>;
            
            setTimeout(() => {
                demo.innerHTML = '<p class="alert alert-success">Form submitted successfully!</p>';
            }, 3000);
        }
        
        function testAsyncLoading() {
            if (window.loadingEnhancements) {
                window.loadingEnhancements.showAsyncOperationLoading('Processing data');
                
                setTimeout(() => {
                    window.loadingEnhancements.hideLoadingOverlay();
                }, 3000);
            }
        }
        
        function testPaymentLoading() {
            if (window.loadingEnhancements) {
                const progressInterval = window.loadingEnhancements.showPaymentProcessing();
                
                setTimeout(() => {
                    window.loadingEnhancements.updateLoadingProgress(100);
                    setTimeout(() => {
                        window.loadingEnhancements.hideLoadingOverlay();
                        clearInterval(progressInterval);
                    }, 1000);
                }, 4000);
            }
        }
        
        function testUploadLoading() {
            const demo = document.getElementById('loading-demo');
            demo.innerHTML = <?php echo json_encode(LoadingStates::imageUploadLoading()); ?>;
            
            // Simulate upload progress
            let progress = 0;
            const progressBar = demo.querySelector('.progress-fill');
            const interval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress > 100) progress = 100;
                
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        demo.innerHTML = '<p class="alert alert-success">Upload completed successfully!</p>';
                    }, 500);
                }
            }, 300);
        }
        
        // Test functions for empty states
        function testEmptyCart() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::emptyCart()); ?>;
        }
        
        function testNoProducts() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::noProducts()); ?>;
        }
        
        function testNoSearchResults() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::noSearchResults('organic tomatoes')); ?>;
        }
        
        function testConnectionError() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::connectionError()); ?>;
        }
        
        function testMaintenanceMode() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::maintenance()); ?>;
        }
        
        function testOfflineMode() {
            const demo = document.getElementById('empty-demo');
            demo.innerHTML = <?php echo json_encode(EmptyStates::offlineMode()); ?>;
        }
        
        // Test functions for images
        function loadOptimizedImages() {
            const demo = document.getElementById('image-demo');
            demo.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <?php echo $polish->optimizedImage('uploads/products/sample1.jpg', 'Sample Product 1', 'optimized-image'); ?>
                    <?php echo $polish->optimizedImage('uploads/products/sample2.jpg', 'Sample Product 2', 'optimized-image'); ?>
                    <?php echo $polish->optimizedImage('uploads/products/sample3.jpg', 'Sample Product 3', 'optimized-image'); ?>
                </div>
            `;
        }
        
        function clearImages() {
            const demo = document.getElementById('image-demo');
            demo.innerHTML = '<p>Images cleared</p>';
        }
        
        // Prevent actual form submission for demo
        document.getElementById('test-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            setTimeout(() => {
                if (window.loadingEnhancements) {
                    window.loadingEnhancements.hideLoadingOverlay();
                }
                alert('Form submitted successfully! (Demo only)');
            }, 3000);
        });
        
        console.log('Polish features test page loaded successfully');
    </script>
</body>
</html>