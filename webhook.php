<?php
// Set your webhook secret
$webhookSecret = 'omkar@123';


//$logFile = __DIR__ . '/webhook_log.txt';
// function logData($label, $data, $file) {
//     file_put_contents($file, "[$label] " . date('Y-m-d H:i:s') . "\n" . print_r($data, true) . "\n\n", FILE_APPEND);
// }

// Fetch and log incoming request
$input = file_get_contents('php://input');
$headers = getallheaders();
$signature = $headers['X-Razorpay-Signature'] ?? '';

//  logData('Raw Body', $input, $logFile);
//  logData('Headers', $headers, $logFile);

// Verify signature
$expectedSignature = hash_hmac('sha256', $input, $webhookSecret);
if (!hash_equals($expectedSignature, $signature)) {
    // logData('Signature Verification', 'FAILED', $logFile);
    http_response_code(400);
    echo 'Invalid signature';
    exit;
}
// logData('Signature Verification', 'PASSED', $logFile);

// Decode payload
$data = json_decode($input, true);
// logData('Parsed JSON', $data, $logFile);

// Check event
if (!isset($data['event']) || $data['event'] !== 'payment.captured') {
//logData('Event Check', 'Event is not payment.captured', $logFile);
    http_response_code(200);
    exit;
}

// Extract payment data
$paymentEntity = $data['payload']['payment']['entity'] ?? null;

if (!$paymentEntity) {
     logData('Payment Data', 'No payment entity found', $logFile);
    http_response_code(200);
    exit;
}

// Safely extract data
$paymentId    = $paymentEntity['id'] ?? '';
$amount       = $paymentEntity['amount'] / 100;
$sale_id      = $paymentEntity['notes']['sale_id'] ?? '';
$customerName = $paymentEntity['notes']['customer_name'] ?? '';
$createdAt    = date('Y-m-d H:i:s');

// DB connection
$mysqli = new mysqli("localhost", "aamramm_inventory", "y*O4q123Egw$", "aamramm_inv");
if ($mysqli->connect_error) {
     //logData('DB Connection Error', $mysqli->connect_error, $logFile);
    http_response_code(500);
    exit;
}

// Fetch the count of purchase_payments records to determine ReceiptNo
$result = $mysqli->query("SELECT MAX(ReceiptNo) AS max_receipt FROM purchase_payments");
$row = $result->fetch_assoc();
$nextBillNo = ($row['max_receipt'] ?? 0) + 1;

// Insert into razorpay_history
$sqlHistory = "INSERT INTO razorpay_history (transaction_id, order_id, signature, paid_amount, customer_name, created_at ,web_flag)
               VALUES (?, ?, ?, ?, ?, ?,'webhook')";
$stmtHistory = $mysqli->prepare($sqlHistory);

if (!$stmtHistory) {
    // logData('DB Prepare Error (razorpay_history)', $mysqli->error, $logFile);
    http_response_code(500);
    exit;
}

$stmtHistory->bind_param("sssiss", $paymentId, $sale_id, $signature, $amount, $customerName, $createdAt);
$stmtHistory->execute();
$stmtHistory->close();

// Insert into purchase_payments
$purchaseDate = date("Y-m-d");
$season = date("Y");

$sqlPayment = "INSERT INTO purchase_payments (user_id, ReceiptNo, PurchaseDate, customer_name, amt_pay, totalvalue, mode, complete_flag, created_at,season)
               VALUES ('razorpay', ?, ?, ?, ?, ?, 'online', 'online', ?,?)";
$stmtPayment = $mysqli->prepare($sqlPayment);

if (!$stmtPayment) {
    // logData('DB Prepare Error (purchase_payments)', $mysqli->error, $logFile);
    http_response_code(500);
    exit;
}

$stmtPayment->bind_param("issdddi", $nextBillNo, $purchaseDate, $customerName, $amount, $amount, $createdAt,$season);

if ($stmtPayment->execute()) {
     //logData('DB Insert (purchase_payments)', 'Success', $logFile);
    $insertPaymentId = $stmtPayment->insert_id;
} else {
    // logData('DB Insert Error (purchase_payments)', $stmtPayment->error, $logFile);
    http_response_code(500);
    exit;
}

$stmtPayment->close();

// Insert into purchase_payment_info
$sqlPaymentInfo = "INSERT INTO purchase_payment_info (pid, Invoicenumber, amount, created_at, updated_at)
                   VALUES (?, ?, ?, ?, ?)";
$stmtPaymentInfo = $mysqli->prepare($sqlPaymentInfo);

if (!$stmtPaymentInfo) {
   // logData('DB Prepare Error (purchase_payment_info)', $mysqli->error, $logFile);
    http_response_code(500);
    exit;
}

$stmtPaymentInfo->bind_param("iidsd", $insertPaymentId, $sale_id, $amount, $createdAt, $createdAt);

if ($stmtPaymentInfo->execute()) {
    // logData('DB Insert (purchase_payment_info)', 'Success', $logFile);
} else {
    // logData('DB Insert Error (purchase_payment_info)', $stmtPaymentInfo->error, $logFile);
}

$stmtPaymentInfo->close();
$mysqli->close();

http_response_code(200);
echo 'Webhook processed successfully';
