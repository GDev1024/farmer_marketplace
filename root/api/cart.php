<?php
// ==================== api/cart.php ====================
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'consumer') {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}

$db = Config::getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $listingId = (int)$_POST['listing_id'];
        $quantity = (int)$_POST['quantity'];
        
        // Check if item exists and has stock
        $stmt = $db->prepare("SELECT quantity FROM listings WHERE id = ? AND status = 'active'");
        $stmt->execute([$listingId]);
        $listing = $stmt->fetch();
        
        if (!$listing || $listing['quantity'] < $quantity) {
            redirect('../product.php?id=' . $listingId . '&error=out_of_stock');
        }
        
        // Add or update cart
        $stmt = $db->prepare("
            INSERT INTO cart (user_id, listing_id, quantity) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ");
        $stmt->execute([$user['id'], $listingId, $quantity, $quantity]);
        
        redirect('../cart.php');
    }
    
    if ($action === 'update') {
        $listingId = (int)$_POST['listing_id'];
        $quantity = (int)$_POST['quantity'];
        
        $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND listing_id = ?");
        $stmt->execute([$quantity, $user['id'], $listingId]);
        
        redirect('../cart.php');
    }
    
    if ($action === 'remove') {
        $listingId = (int)$_POST['listing_id'];
        
        $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND listing_id = ?");
        $stmt->execute([$user['id'], $listingId]);
        
        redirect('../cart.php');
    }
}

redirect('../cart.php');
?>