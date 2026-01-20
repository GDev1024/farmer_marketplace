<?php
/**
 * Payment Handler Class
 * Handles Stripe and PayPal payment processing
 */
class PaymentHandler {
    
    private $stripeSecretKey;
    private $stripePublishableKey;
    private $paypalClientId;
    private $paypalClientSecret;
    private $paypalMode; // 'sandbox' or 'live'
    
    public function __construct() {
        // Load from environment variables
        $this->stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
        $this->stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? getenv('STRIPE_PUBLISHABLE_KEY');
        $this->paypalClientId = $_ENV['PAYPAL_CLIENT_ID'] ?? getenv('PAYPAL_CLIENT_ID');
        $this->paypalClientSecret = $_ENV['PAYPAL_CLIENT_SECRET'] ?? getenv('PAYPAL_CLIENT_SECRET');
        $this->paypalMode = $_ENV['PAYPAL_MODE'] ?? getenv('PAYPAL_MODE') ?? 'sandbox';
    }
    
    /**
     * Create Stripe Payment Intent
     */
    public function createStripePaymentIntent($amount, $currency = 'usd', $metadata = []) {
        if (!$this->stripeSecretKey) {
            return ['success' => false, 'error' => 'Stripe not configured'];
        }
        
        $url = 'https://api.stripe.com/v1/payment_intents';
        
        $data = [
            'amount' => $amount * 100, // Convert to cents
            'currency' => $currency,
            'metadata' => $metadata,
            'automatic_payment_methods[enabled]' => 'true'
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->stripeSecretKey,
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $result['id']
            ];
        } else {
            $error = json_decode($response, true);
            return [
                'success' => false,
                'error' => $error['error']['message'] ?? 'Payment failed'
            ];
        }
    }
    
    /**
     * Verify Stripe Payment
     */
    public function verifyStripePayment($paymentIntentId) {
        if (!$this->stripeSecretKey) {
            return ['success' => false, 'error' => 'Stripe not configured'];
        }
        
        $url = "https://api.stripe.com/v1/payment_intents/{$paymentIntentId}";
        
        $headers = [
            'Authorization: Bearer ' . $this->stripeSecretKey
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLOPT_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'status' => $result['status'],
                'amount' => $result['amount'] / 100, // Convert from cents
                'metadata' => $result['metadata']
            ];
        } else {
            return ['success' => false, 'error' => 'Payment verification failed'];
        }
    }
    
    /**
     * Create PayPal Order
     */
    public function createPayPalOrder($amount, $currency = 'USD', $orderId = null) {
        if (!$this->paypalClientId || !$this->paypalClientSecret) {
            return ['success' => false, 'error' => 'PayPal not configured'];
        }
        
        // Get access token first
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'error' => 'PayPal authentication failed'];
        }
        
        $baseUrl = $this->paypalMode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
        
        $url = $baseUrl . '/v2/checkout/orders';
        
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $orderId ?? uniqid(),
                'amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($amount, 2, '.', '')
                ]
            ]],
            'application_context' => [
                'return_url' => $_ENV['APP_URL'] . '/index.php?page=payment-success',
                'cancel_url' => $_ENV['APP_URL'] . '/index.php?page=payment-cancel'
            ]
        ];
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 201) {
            $result = json_decode($response, true);
            $approvalUrl = '';
            foreach ($result['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }
            
            return [
                'success' => true,
                'order_id' => $result['id'],
                'approval_url' => $approvalUrl
            ];
        } else {
            $error = json_decode($response, true);
            return [
                'success' => false,
                'error' => $error['message'] ?? 'PayPal order creation failed'
            ];
        }
    }
    
    /**
     * Capture PayPal Payment
     */
    public function capturePayPalPayment($orderId) {
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'error' => 'PayPal authentication failed'];
        }
        
        $baseUrl = $this->paypalMode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
        
        $url = $baseUrl . "/v2/checkout/orders/{$orderId}/capture";
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 201) {
            $result = json_decode($response, true);
            return [
                'success' => true,
                'status' => $result['status'],
                'capture_id' => $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null
            ];
        } else {
            return ['success' => false, 'error' => 'PayPal capture failed'];
        }
    }
    
    /**
     * Get PayPal Access Token
     */
    private function getPayPalAccessToken() {
        $baseUrl = $this->paypalMode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
        
        $url = $baseUrl . '/v1/oauth2/token';
        
        $headers = [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Authorization: Basic ' . base64_encode($this->paypalClientId . ':' . $this->paypalClientSecret)
        ];
        
        $data = 'grant_type=client_credentials';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['access_token'];
        }
        
        return false;
    }
    
    /**
     * Get Stripe Publishable Key
     */
    public function getStripePublishableKey() {
        return $this->stripePublishableKey;
    }
    
    /**
     * Get PayPal Client ID
     */
    public function getPayPalClientId() {
        return $this->paypalClientId;
    }
    
    /**
     * Check if payment methods are configured
     */
    public function getAvailablePaymentMethods() {
        $methods = [];
        
        if ($this->stripeSecretKey && $this->stripePublishableKey) {
            $methods[] = 'stripe';
        }
        
        if ($this->paypalClientId && $this->paypalClientSecret) {
            $methods[] = 'paypal';
        }
        
        return $methods;
    }
}
?>