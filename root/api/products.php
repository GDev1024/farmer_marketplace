<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/aws-image-handler.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'farmer') {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}

$db = Config::getDB();

// GET - Fetch single product
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM listings WHERE id = ? AND farmer_id = ?");
    $stmt->execute([$id, $user['id']]);
    $product = $stmt->fetch();
    
    if ($product) {
        jsonResponse($product);
    } else {
        jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
    }
}

// POST - Create, Update, Delete, Toggle Status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    // Handle JSON requests (delete, toggle_status)
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'delete') {
            $id = (int)$input['id'];
            
            // Get image URL to delete
            $stmt = $db->prepare("SELECT image_url FROM listings WHERE id = ? AND farmer_id = ?");
            $stmt->execute([$id, $user['id']]);
            $product = $stmt->fetch();
            
            if ($product) {
                // Delete image if exists
                if ($product['image_url']) {
                    $imageHandler = new AWSImageHandler();
                    $imageHandler->deleteImage($product['image_url']);
                }
                
                // Delete product
                $stmt = $db->prepare("DELETE FROM listings WHERE id = ? AND farmer_id = ?");
                $stmt->execute([$id, $user['id']]);
                
                jsonResponse(['success' => true]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }
        }
        
        if ($action === 'toggle_status') {
            $id = (int)$input['id'];
            $status = sanitizeInput($input['status']);
            
            $stmt = $db->prepare("UPDATE listings SET status = ? WHERE id = ? AND farmer_id = ?");
            $stmt->execute([$status, $id, $user['id']]);
            
            jsonResponse(['success' => true]);
        }
    }
    
    // Handle form data requests (create, update)
    else {
        $action = $_POST['action'] ?? '';
        
        $name = sanitizeInput($_POST['name']);
        $category = sanitizeInput($_POST['category']);
        $price = (float)$_POST['price'];
        $quantity = (int)$_POST['quantity'];
        $unit = sanitizeInput($_POST['unit']);
        $description = sanitizeInput($_POST['description']);
        
        $imageUrl = null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $imageHandler = new AWSImageHandler();
                $imageUrl = $imageHandler->uploadImage($_FILES['image'], 'products');
            } catch (Exception $e) {
                jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
        
        if ($action === 'create') {
            $stmt = $db->prepare("
                INSERT INTO listings (farmer_id, name, category, price, quantity, unit, description, image_url, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$user['id'], $name, $category, $price, $quantity, $unit, $description, $imageUrl]);
            
            jsonResponse(['success' => true, 'id' => $db->lastInsertId()]);
        }
        
        if ($action === 'update') {
            $id = (int)$_POST['id'];
            
            // Verify ownership
            $stmt = $db->prepare("SELECT id, image_url FROM listings WHERE id = ? AND farmer_id = ?");
            $stmt->execute([$id, $user['id']]);
            $existing = $stmt->fetch();
            
            if (!$existing) {
                jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }
            
            // If new image uploaded, delete old one
            if ($imageUrl && $existing['image_url']) {
                $imageHandler = new AWSImageHandler();
                $imageHandler->deleteImage($existing['image_url']);
            }
            
            // Keep old image if no new one uploaded
            if (!$imageUrl) {
                $imageUrl = $existing['image_url'];
            }
            
            $stmt = $db->prepare("
                UPDATE listings 
                SET name = ?, category = ?, price = ?, quantity = ?, unit = ?, description = ?, image_url = ?
                WHERE id = ? AND farmer_id = ?
            ");
            $stmt->execute([$name, $category, $price, $quantity, $unit, $description, $imageUrl, $id, $user['id']]);
            
            jsonResponse(['success' => true]);
        }
    }
}

jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
?>