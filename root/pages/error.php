<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Grenada Farmers Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: var(--color-gray-50);
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: var(--space-8);
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
        }
        .error-icon {
            font-size: 5rem;
            margin-bottom: var(--space-4);
            opacity: 0.6;
        }
        .error-code {
            font-size: 3rem;
            font-weight: 900;
            color: var(--color-primary);
            margin-bottom: var(--space-2);
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: var(--space-4);
        }
        .error-message {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            line-height: 1.6;
            margin-bottom: var(--space-6);
        }
        .error-actions {
            display: flex;
            gap: var(--space-3);
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-details {
            margin-top: var(--space-6);
            padding: var(--space-4);
            background: var(--color-gray-50);
            border-radius: var(--border-radius);
            text-align: left;
            font-size: 0.875rem;
            color: var(--color-text-muted);
        }
        .error-details summary {
            cursor: pointer;
            font-weight: 600;
            margin-bottom: var(--space-2);
        }
        .error-details pre {
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body class="page">
    <div class="error-container">
        <?php
        $errorCode = http_response_code() ?: 500;
        $errorMessages = [
            400 => ['icon' => '⚠️', 'title' => 'Bad Request', 'message' => 'The request could not be understood by the server.'],
            401 => ['icon' => '🔐', 'title' => 'Unauthorized', 'message' => 'You need to log in to access this resource.'],
            403 => ['icon' => '🚫', 'title' => 'Forbidden', 'message' => 'You don\'t have permission to access this resource.'],
            404 => ['icon' => '🔍', 'title' => 'Page Not Found', 'message' => 'The page you\'re looking for doesn\'t exist or has been moved.'],
            500 => ['icon' => '💥', 'title' => 'Server Error', 'message' => 'Something went wrong on our end. We\'re working to fix it.'],
            503 => ['icon' => '🔧', 'title' => 'Service Unavailable', 'message' => 'The service is temporarily unavailable. Please try again later.']
        ];
        
        $error = $errorMessages[$errorCode] ?? $errorMessages[500];
        ?>
        
        <div class="error-icon"><?= $error['icon'] ?></div>
        <div class="error-code"><?= $errorCode ?></div>
        <h1 class="error-title"><?= $error['title'] ?></h1>
        <p class="error-message"><?= $error['message'] ?></p>
        
        <div class="error-actions">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <span>← Go Back</span>
            </a>
            <a href="index.php" class="btn btn-primary">
                <span>🏠 Home</span>
            </a>
            <?php if ($errorCode === 404): ?>
                <a href="index.php?page=browse" class="btn btn-secondary">
                    <span>🛍️ Browse Products</span>
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
            <details class="error-details">
                <summary>Technical Details</summary>
                <pre><?php
                    echo "Error Code: " . $errorCode . "\n";
                    echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
                    echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";
                    echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
                    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
                    
                    if (isset($exception)) {
                        echo "\nException: " . $exception->getMessage() . "\n";
                        echo "File: " . $exception->getFile() . "\n";
                        echo "Line: " . $exception->getLine() . "\n";
                    }
                ?></pre>
            </details>
        <?php endif; ?>
        
        <div style="margin-top: var(--space-6); padding-top: var(--space-4); border-top: 1px solid var(--color-gray-200);">
            <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0;">
                If this problem persists, please contact our support team.
            </p>
        </div>
    </div>
    
    <script>
        // Auto-refresh for server errors after 30 seconds
        if (<?= $errorCode ?> >= 500) {
            setTimeout(() => {
                if (confirm('Would you like to try refreshing the page?')) {
                    location.reload();
                }
            }, 30000);
        }
        
        // Track error for analytics (if available)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exception', {
                'description': 'Error <?= $errorCode ?>: <?= $error['title'] ?>',
                'fatal': <?= $errorCode >= 500 ? 'true' : 'false' ?>
            });
        }
    </script>
</body>
</html>