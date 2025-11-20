<?php
session_start();

// Database Connection
$host = 'localhost';
$db = 'grenada_farmers';
$user = 'root';
$pass = '';

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

    $stmt = $pdo->prepare("INSERT INTO listings (user_id, product_name, category, price, unit, quantity, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    try {
        $stmt->execute([$_SESSION['userId'], $productName, $category, $price, $unit, $quantity, $description]);
        setMessage('Product listed successfully!', 'success');
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

// Default redirect
redirect('landing');
?>