<?php
session_start();

// Load configuration
require_once 'includes/Config.php';
Config::load();

// Include AWS-compatible ImageHandler class
require_once 'includes/AWSImageHandler.php';

// Database Connection
$dbConfig = Config::getDatabase();
$host = $dbConfig['host'];
$db = $dbConfig['name'];
$user = $dbConfig['user'];
$pass = $dbConfig['pass'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Helper Functions
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
}

function redirect($page, $message = null, $type = 'success') {
    if($message) {
        $_SESSION['message'] = $message;
        $_SESSION['messageType'] = $type;
    }
    header("Location: index.php?page=$page");
    exit;
}

function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $type;
}

function getAndClearMessage() {
    $message = $_SESSION['message'] ?? null;
    $type = $_SESSION['messageType'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
    return ['message' => $message, 'type' => $type];
}

// Register Handler
if(isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $farmerID = sanitize($_POST['farmerID'] ?? '');

    $errors = [];

    if(empty($name)) $errors[] = 'Name is required';
    if(empty($email)) $errors[] = 'Email is required';
    if(empty($password)) $errors[] = 'Password is required';
    if($password !== $confirm) $errors[] = 'Passwords do not match';
    if(strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Check if email exists
    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $errors[] = 'Email already registered';
        }
    }

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('register');
    }

    // Insert user
    $hashedPassword = passwordHash($password);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, farmer_id, farmer_verified, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    try {
        $stmt->execute([$name, $email, $hashedPassword, $farmerID, !empty($farmerID) ? 0 : 0]);
        setMessage('Registration successful! Please login.', 'success');
        redirect('login');
    } catch (PDOException $e) {
        setMessage('Registration failed. Please try again.', 'error');
        redirect('register');
    }
}

// Login Handler
if(isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $errors = [];
    if(empty($email)) $errors[] = 'Email is required';
    if(empty($password)) $errors[] = 'Password is required';

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('login');
    }

    $stmt = $pdo->prepare("SELECT id, name, password, farmer_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && passwordVerify($password, $user['password'])) {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['userId'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $email;
        $_SESSION['farmerVerified'] = $user['farmer_verified'];
        
        setMessage('Login successful!', 'success');
        redirect('home');
    } else {
        setMessage('Invalid email or password', 'error');
        redirect('login');
    }
}

// List Product Handler
if(isset($_POST['listProduct'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login to list products', 'error');
        redirect('login');
    }

    $productName = sanitize($_POST['productName']);
    $category = sanitize($_POST['category']);
    $price = floatval($_POST['price']);
    $unit = sanitize($_POST['unit']);
    $quantity = intval($_POST['quantity']);
    $description = sanitize($_POST['description']);

    $errors = [];
    if(empty($productName)) $errors[] = 'Product name is required';
    if(empty($category)) $errors[] = 'Category is required';
    if($price <= 0) $errors[] = 'Price must be greater than 0';
    if(empty($unit)) $errors[] = 'Unit type is required';
    if($quantity < 0) $errors[] = 'Quantity cannot be negative';
    if(empty($description)) $errors[] = 'Description is required';

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('listing');
    }

    // Insert listing first to get ID for image naming
    $stmt = $pdo->prepare("INSERT INTO listings (user_id, product_name, category, price, unit, quantity, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    try {
        $stmt->execute([$_SESSION['userId'], $productName, $category, $price, $unit, $quantity, $description]);
        $listingId = $pdo->lastInsertId();
        
        // Handle image upload if provided
        $imagePath = null;
        $thumbnailPath = null;
        
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
            $imageHandler = new AWSImageHandler();
            $uploadResult = $imageHandler->uploadImage($_FILES['productImage'], $listingId);
            
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['image_path'];
                $thumbnailPath = $uploadResult['thumbnail_path'];
                
                // Update listing with image paths
                $updateStmt = $pdo->prepare("UPDATE listings SET image_path = ?, thumbnail_path = ? WHERE id = ?");
                $updateStmt->execute([$imagePath, $thumbnailPath, $listingId]);
            } else {
                // Image upload failed, but listing was created - show warning
                setMessage('Product listed successfully, but image upload failed: ' . $uploadResult['error'], 'warning');
                redirect('sell');
            }
        }
        
        setMessage('Product listed successfully!' . ($imagePath ? ' Image uploaded.' : ''), 'success');
        redirect('sell');
    } catch (PDOException $e) {
        setMessage('Failed to list product. Please try again.', 'error');
        redirect('listing');
    }
}

// Add to Cart Handler
if(isset($_POST['addToCart'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login to add items to cart', 'error');
        redirect('login');
    }

    $listingId = intval($_POST['listingId']);
    $quantity = intval($_POST['cartQuantity']);

    if($quantity <= 0) {
        setMessage('Quantity must be at least 1', 'error');
        redirect('browse');
    }

    // Initialize cart in session
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if(isset($_SESSION['cart'][$listingId])) {
        $_SESSION['cart'][$listingId] += $quantity;
    } else {
        $_SESSION['cart'][$listingId] = $quantity;
    }

    setMessage('Item added to cart!', 'success');
    redirect('browse');
}

// Update Cart Handler
if(isset($_POST['updateCart'])) {
    $cart = $_POST['quantities'];
    
    foreach($cart as $listingId => $quantity) {
        $quantity = intval($quantity);
        if($quantity <= 0) {
            unset($_SESSION['cart'][$listingId]);
        } else {
            $_SESSION['cart'][$listingId] = $quantity;
        }
    }

    setMessage('Cart updated!', 'success');
    redirect('cart');
}

// Remove from Cart Handler
if(isset($_POST['removeFromCart'])) {
    $listingId = intval($_POST['listingId']);
    unset($_SESSION['cart'][$listingId]);
    setMessage('Item removed from cart', 'success');
    redirect('cart');
}

// Checkout Handler
if(isset($_POST['checkout'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login to checkout', 'error');
        redirect('login');
    }

    if(empty($_SESSION['cart'])) {
        setMessage('Your cart is empty', 'error');
        redirect('cart');
    }

    $totalPrice = 0;
    $orderItems = [];

    // Calculate total and validate
    foreach($_SESSION['cart'] as $listingId => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM listings WHERE id = ? AND quantity >= ?");
        $stmt->execute([$listingId, $quantity]);
        $listing = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$listing) {
            setMessage('Some items are out of stock', 'error');
            redirect('cart');
        }

        $totalPrice += $listing['price'] * $quantity;
        $orderItems[] = ['listingId' => $listingId, 'quantity' => $quantity];
    }

    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())");
    
    try {
        $stmt->execute([$_SESSION['userId'], $totalPrice]);
        $orderId = $pdo->lastInsertId();

        // Create order items
        foreach($orderItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, listing_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $item['listingId'], $item['quantity']]);

            // Update listing quantity
            $stmt = $pdo->prepare("UPDATE listings SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['listingId']]);
        }

        $_SESSION['cart'] = [];
        setMessage("Order placed successfully! Order ID: $orderId. Total: EC\$$totalPrice", 'success');
        redirect('orders');
    } catch (PDOException $e) {
        setMessage('Checkout failed. Please try again.', 'error');
        redirect('cart');
    }
}

// Send Message Handler
if(isset($_POST['sendMessage'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login to send messages', 'error');
        redirect('login');
    }

    $receiverId = intval($_POST['receiverId']);
    $message = sanitize($_POST['message']);

    if(empty($message)) {
        setMessage('Message cannot be empty', 'error');
        redirect('messages');
    }

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
    
    try {
        $stmt->execute([$_SESSION['userId'], $receiverId, $message]);
        setMessage('Message sent!', 'success');
        redirect('messages');
    } catch (PDOException $e) {
        setMessage('Failed to send message', 'error');
        redirect('messages');
    }
}

// Update Profile Handler
if(isset($_POST['updateProfile'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login', 'error');
        redirect('login');
    }

    $name = sanitize($_POST['name']);
    $farmerID = sanitize($_POST['farmerID']);

    if(empty($name)) {
        setMessage('Name cannot be empty', 'error');
        redirect('profile');
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, farmer_id = ? WHERE id = ?");
    
    try {
        $stmt->execute([$name, $farmerID, $_SESSION['userId']]);
        $_SESSION['name'] = $name;
        setMessage('Profile updated successfully!', 'success');
        redirect('profile');
    } catch (PDOException $e) {
        setMessage('Failed to update profile', 'error');
        redirect('profile');
    }
}

// Edit Listing Handler
if(isset($_POST['editListing'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login', 'error');
        redirect('login');
    }

    $listingId = intval($_POST['listingId']);
    $productName = sanitize($_POST['productName']);
    $category = sanitize($_POST['category']);
    $price = floatval($_POST['price']);
    $unit = sanitize($_POST['unit']);
    $quantity = intval($_POST['quantity']);
    $description = sanitize($_POST['description']);

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM listings WHERE id = ? AND user_id = ?");
    $stmt->execute([$listingId, $_SESSION['userId']]);
    if(!$stmt->fetch()) {
        setMessage('Listing not found or access denied', 'error');
        redirect('sell');
    }

    $errors = [];
    if(empty($productName)) $errors[] = 'Product name is required';
    if(empty($category)) $errors[] = 'Category is required';
    if($price <= 0) $errors[] = 'Price must be greater than 0';
    if(empty($unit)) $errors[] = 'Unit type is required';
    if($quantity < 0) $errors[] = 'Quantity cannot be negative';
    if(empty($description)) $errors[] = 'Description is required';

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('sell');
    }

    try {
        // Update listing
        $stmt = $pdo->prepare("UPDATE listings SET product_name = ?, category = ?, price = ?, unit = ?, quantity = ?, description = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$productName, $category, $price, $unit, $quantity, $description, $listingId, $_SESSION['userId']]);

        // Handle image upload if provided
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
            $imageHandler = new AWSImageHandler();
            $uploadResult = $imageHandler->uploadImage($_FILES['productImage'], $listingId);
            
            if ($uploadResult['success']) {
                // Get old image paths for deletion
                $stmt = $pdo->prepare("SELECT image_path, thumbnail_path FROM listings WHERE id = ?");
                $stmt->execute([$listingId]);
                $oldPaths = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Update with new image paths
                $updateStmt = $pdo->prepare("UPDATE listings SET image_path = ?, thumbnail_path = ? WHERE id = ?");
                $updateStmt->execute([$uploadResult['image_path'], $uploadResult['thumbnail_path'], $listingId]);
                
                // Delete old images
                if ($oldPaths['image_path'] || $oldPaths['thumbnail_path']) {
                    $imageHandler->deleteImage($oldPaths['image_path'], $oldPaths['thumbnail_path']);
                }
            }
        }

        setMessage('Listing updated successfully!', 'success');
        redirect('sell');
    } catch (PDOException $e) {
        setMessage('Failed to update listing', 'error');
        redirect('sell');
    }
}

// Activate/Deactivate Listing Handler
if(isset($_POST['activateListing']) || isset($_POST['deactivateListing'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login', 'error');
        redirect('login');
    }

    $listingId = intval($_POST['listingId']);
    $activate = isset($_POST['activateListing']);

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM listings WHERE id = ? AND user_id = ?");
    $stmt->execute([$listingId, $_SESSION['userId']]);
    if(!$stmt->fetch()) {
        setMessage('Listing not found or access denied', 'error');
        redirect('sell');
    }

    try {
        $stmt = $pdo->prepare("UPDATE listings SET is_active = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$activate ? 1 : 0, $listingId, $_SESSION['userId']]);
        
        $message = $activate ? 'Listing activated successfully!' : 'Listing deactivated successfully!';
        setMessage($message, 'success');
        redirect('sell');
    } catch (PDOException $e) {
        setMessage('Failed to update listing status', 'error');
        redirect('sell');
    }
}

// Restock Listing Handler
if(isset($_POST['restockListing'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login', 'error');
        redirect('login');
    }

    $listingId = intval($_POST['listingId']);
    $addQuantity = intval($_POST['addQuantity']);

    if($addQuantity <= 0) {
        setMessage('Quantity to add must be greater than 0', 'error');
        redirect('sell');
    }

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM listings WHERE id = ? AND user_id = ?");
    $stmt->execute([$listingId, $_SESSION['userId']]);
    if(!$stmt->fetch()) {
        setMessage('Listing not found or access denied', 'error');
        redirect('sell');
    }

    try {
        $stmt = $pdo->prepare("UPDATE listings SET quantity = quantity + ?, is_active = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$addQuantity, $listingId, $_SESSION['userId']]);
        
        setMessage("Added $addQuantity units to stock and activated listing!", 'success');
        redirect('sell');
    } catch (PDOException $e) {
        setMessage('Failed to restock listing', 'error');
        redirect('sell');
    }
}

// Delete Listing Handler
if(isset($_POST['deleteListing'])) {
    if(!$_SESSION['isLoggedIn']) {
        setMessage('Please login', 'error');
        redirect('login');
    }

    $listingId = intval($_POST['listingId']);

    // Verify ownership and get image paths
    $stmt = $pdo->prepare("SELECT image_path, thumbnail_path FROM listings WHERE id = ? AND user_id = ?");
    $stmt->execute([$listingId, $_SESSION['userId']]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$listing) {
        setMessage('Listing not found or access denied', 'error');
        redirect('sell');
    }

    try {
        // Delete listing from database
        $stmt = $pdo->prepare("DELETE FROM listings WHERE id = ? AND user_id = ?");
        $stmt->execute([$listingId, $_SESSION['userId']]);
        
        // Delete associated images
        if ($listing['image_path'] || $listing['thumbnail_path']) {
            $imageHandler = new AWSImageHandler();
            $imageHandler->deleteImage($listing['image_path'], $listing['thumbnail_path']);
        }
        
        setMessage('Listing deleted successfully!', 'success');
        redirect('sell');
    } catch (PDOException $e) {
        setMessage('Failed to delete listing', 'error');
        redirect('sell');
    }
}

// Default redirect
redirect('landing');
?>