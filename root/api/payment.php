<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require 'vendor/autoload.php'; // Composer autoload for Stripe SDK

use Stripe\Stripe;
use Stripe\PaymentIntent;

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'consumer') {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}

$db = Config::getDB();
Stripe::setApiKey(Config::STRIPE_SECRET_KEY);

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// Create Stripe Payment Intent
if ($action === 'create_stripe_intent') {
    try {
        $amount = (int)$input['amount'];
        
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $user['id'],
                'address' => $input['address']
            ]
        ]);
        
        jsonResponse(['clientSecret' => $paymentIntent->client_secret]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
    }
}

// Complete Order with Atomic Transaction
if ($action === 'complete_order') {
    try {
        $paymentMethod = sanitizeInput($input['payment_method']);
        $paymentIntentId = sanitizeInput($input['payment_intent_id']);
        $deliveryAddress = sanitizeInput($input['delivery_address']);
        
        // Start MySQL Transaction for atomic operation
        $db->beginTransaction();
        
        try {
            // 1. Get cart items with stock validation
            $stmt = $db->prepare("
                SELECT c.listing_id, c.quantity, l.price, l.quantity as stock, l.name
                FROM cart c
                JOIN listings l ON c.listing_id = l.id
                WHERE c.user_id = ?
                FOR UPDATE  -- Lock rows for update
            ");
            $stmt->execute([$user['id']]);
            $cartItems = $stmt->fetchAll();
            
            if (empty($cartItems)) {
                throw new Exception('Cart is empty');
            }
            
            // 2. Validate stock availability
            foreach ($cartItems as $item) {
                if ($item['stock'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$item['name']}");
                }
            }
            
            // 3. Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // 4. Create order
            $stmt = $db->prepare("
                INSERT INTO orders (customer_id, total_amount, payment_method, payment_intent_id, payment_status, delivery_address, status)
                VALUES (?, ?, ?, ?, 'completed', ?, 'confirmed')
            ");
            $stmt->execute([$user['id'], $total, $paymentMethod, $paymentIntentId, $deliveryAddress]);
            $orderId = $db->lastInsertId();
            
            // 5. Create order items and decrement stock atomically
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, listing_id, quantity, price_at_purchase)
                VALUES (?, ?, ?, ?)
            ");
            
            $updateStockStmt = $db->prepare("
                UPDATE listings 
                SET quantity = quantity - ?,
                    status = CASE WHEN quantity - ? <= 0 THEN 'out_of_stock' ELSE status END
                WHERE id = ?
            ");
            
            foreach ($cartItems as $item) {
                // Insert order item
                $stmt->execute([
                    $orderId,
                    $item['listing_id'],
                    $item['quantity'],
                    $item['price']
                ]);
                
                // Decrement stock
                $updateStockStmt->execute([
                    $item['quantity'],
                    $item['quantity'],
                    $item['listing_id']
                ]);
            }
            
            // 6. Clear cart
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Commit transaction - all or nothing
            $db->commit();
            
            jsonResponse([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Order completed successfully'
            ]);
            
        } catch (Exception $e) {
            // Rollback on any error
            $db->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        jsonResponse([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}

jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);