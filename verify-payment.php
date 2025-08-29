<?php
// This is a simple example. In production, use proper error handling and logging

// Get Razorpay webhook data
$input = file_get_contents('php://input');
$webhook_data = json_decode($input, true);

// Your Razorpay API credentials
$key_id = "rzp_test_YOUR_KEY_ID"; 
$key_secret = "YOUR_KEY_SECRET";

// Verify signature to ensure the webhook is from Razorpay
$received_signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Signature verification
$expected_signature = hash_hmac('sha256', $input, $key_secret);
if ($received_signature === $expected_signature) {
    // Signature is valid
    $payment_id = $webhook_data['payload']['payment']['entity']['id'] ?? '';
    $amount = $webhook_data['payload']['payment']['entity']['amount'] ?? 0;
    $plan_id = $webhook_data['payload']['payment']['entity']['notes']['plan_id'] ?? '';
    
    // Process the successful payment
    // 1. Update your database
    // 2. Send email to customer with template access
    // 3. Log the successful payment
    
    // Example email sending (basic)
    $to = $webhook_data['payload']['payment']['entity']['email'];
    $subject = "Your Kalpvruksh AI Templates";
    
    switch ($plan_id) {
        case 'starter':
            $template_link = "https://your-website.com/downloads/starter-templates.zip";
            break;
        case 'pro':
            $template_link = "https://your-website.com/downloads/pro-templates.zip";
            break;
        case 'ultimate':
            $template_link = "https://your-website.com/downloads/ultimate-templates.zip";
            break;
        default:
            $template_link = "https://your-website.com/downloads/templates.zip";
    }
    
    $message = "Thank you for your purchase! Download your templates here: $template_link";
    mail($to, $subject, $message);
    
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    // Invalid signature
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
}
?>
