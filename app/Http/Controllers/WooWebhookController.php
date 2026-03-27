<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\sale_details;
use App\Models\sale_order;
use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\Product;
use App\Models\Product_details;
use App\Models\State;
use Illuminate\Support\Facades\DB;

class WooWebhookController extends Controller

{

  public function handleOrder(Request $request)
{
    $logFile = __DIR__ . '/order_log.txt';

    $payload = $request->getContent();
    $data = json_decode($payload, true);

    if (!$data) {
        file_put_contents($logFile, "❌ Invalid JSON\n\n", FILE_APPEND);
        return response()->json(['message' => 'Invalid data'], 400);
    }

    $orderId = $data['id'] ?? '';
    $orderTotal = $data['total'] ?? 0;
    $currency = $data['currency'] ?? '';

    file_put_contents($logFile,
        "================ WEBHOOK ORDER =================\n" .
        "Order ID: " . $orderId . "\n" .
        "Date: " . ($data['date_created'] ?? '') . "\n" .
        "Currency: " . $currency . "\n" .
        "💰 Total: " . $orderTotal . "\n\n",
        FILE_APPEND
    );
    file_put_contents($logFile,
        "================ NEW WEBHOOK =================\n" .
        "TIME: " . date('Y-m-d H:i:s') . "\n" .
        "HEADERS:\n" . json_encode($request->headers->all(), JSON_PRETTY_PRINT) . "\n\n" .
        "RAW BODY:\n" . $payload . "\n\n" .
        "PARSED BODY:\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n\n",
        FILE_APPEND
);
// =========================
    // CUSTOMER DETAILS
    // =========================
    $billing = $data['billing'] ?? [];

    $fullAddress = trim(
        ($billing['address_1'] ?? '') . ' ' .
        ($billing['address_2'] ?? '') . ' ' .
        ($billing['city'] ?? '') . ' ' .
        ($billing['state'] ?? '') . ' ' .
        ($billing['postcode'] ?? '') . ' ' .
        ($billing['country'] ?? '')
    );

    file_put_contents($logFile,
        "👤 CUSTOMER:\n" .
        "Name: " . ($billing['first_name'] ?? '') . " " . ($billing['last_name'] ?? '') . "\n" .
        "Email: " . ($billing['email'] ?? '') . "\n" .
        "Phone: " . ($billing['phone'] ?? '') . "\n" .
        "Address: " . $fullAddress . "\n\n",
        FILE_APPEND
    );

    // =========================
    // GET PARENT SKU FROM LINE ITEMS
    // =========================
    $parentSkuMap = [];

    if (!empty($data['line_items'])) {
        foreach ($data['line_items'] as $li) {
            if (isset($li['parent_sku'])) {
                // index like 227 etc
                $parentSkuMap = $li['parent_sku'];
                break;
            }
        }
    }

    // =========================
    // LOG FUNCTION
    // =========================
    $logItem = function ($item, $key = null) use ($logFile, $data, $parentSkuMap) {

        $productName = $item['name'] ?? $item['item_name'] ?? '';
        $productId   = $item['product_id'] ?? null;
        $sku         = $item['sku'] ?? '';
        $quantity    = $item['quantity'] ?? 0;

        // price
        $price = $item['total'] ?? ($item['price'] ?? 0);

        $variationId = $item['variation_id'] ?? null;

        // =========================
        // VARIANT (SIZE)
        // =========================
        $variant = '';

        if (!empty($item['meta_data'])) {
            foreach ($item['meta_data'] as $meta) {
                if (strpos($meta['key'] ?? '', 'pa_') === 0) {
                    $variant = $meta['display_value'] ?? $meta['value'] ?? '';
                    break;
                }
            }
        }

        // fallback (for custom payload)
        if (empty($variant) && isset($item['variant'])) {
            $variant = $item['variant'];
        }

        // =========================
        // PARENT SKU
        // =========================
        $parentSku = '';

        if (isset($data['line_items'])) {
            foreach ($data['line_items'] as $li) {
                if (isset($li['parent_sku'])) {
                    $parentSku = $li['parent_sku'];
                    break;
                }
            }
        }

        // =========================
        // LOG
        // =========================
        file_put_contents($logFile,
            "🛒 PRODUCT ITEM:\n" .
            "Key Index: " . ($key ?? '-') . "\n" .
            "Product Name: $productName\n" .
            "Product ID: $productId\n" .
            "Variation ID: $variationId\n" .
            "Parent SKU: $parentSku\n" .
            "SKU: $sku\n" .
            "Variant (Size): $variant\n" .
            "Price: $price\n" .
            "Quantity: $quantity\n" .
            "--------------------------------------\n",
            FILE_APPEND
        );
    };

    // =========================
    // LINE ITEMS (WooCommerce)
    // =========================
    if (!empty($data['line_items'])) {
        foreach ($data['line_items'] as $key => $item) {

            if (!isset($item['product_id'])) {
                continue;
            }

            $logItem($item, $key);
        }
    }

    // =========================
    // CUSTOM ITEMS (if any)
    // =========================
    if (!empty($data['items'])) {
        foreach ($data['items'] as $key => $item) {
            $logItem($item, $key);
        }
    }

    // =========================
    // END
    // =========================
    file_put_contents($logFile,
        "================ END ORDER =================\n\n",
        FILE_APPEND
    );

    return response()->json([
        'message' => 'Logged successfully'
    ]);
}
// public function handleOrder(Request $request)
// {
//     $logFile = __DIR__ . '/order_log.txt';

//     $payload = $request->getContent();
//     $data = json_decode($payload, true);

//     if (!$data) {
//         file_put_contents($logFile, "❌ Invalid JSON\n\n", FILE_APPEND);
//         return response()->json(['message' => 'Invalid data'], 400);
//     }

//     $orderId = $data['id'] ?? '';
//     $orderTotal = $data['total'] ?? 0;
//     $currency = $data['currency'] ?? '';

//     file_put_contents($logFile,
//         "================ WEBHOOK ORDER =================\n" .
//         "Order ID: " . $orderId . "\n" .
//         "Date: " . ($data['date_created'] ?? '') . "\n" .
//         "Currency: " . $currency . "\n" .
//         "💰 Total: " . $orderTotal . "\n\n",
//         FILE_APPEND
//     );
//     file_put_contents($logFile,
//         "================ NEW WEBHOOK =================\n" .
//         "TIME: " . date('Y-m-d H:i:s') . "\n" .
//         "HEADERS:\n" . json_encode($request->headers->all(), JSON_PRETTY_PRINT) . "\n\n" .
//         "RAW BODY:\n" . $payload . "\n\n" .
//         "PARSED BODY:\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n\n",
//         FILE_APPEND
// );
//     // =========================
//     // DUPLICATE CHECK
//     // =========================
//     if (sale_details::where('import_no', $orderId)->exists()) {
//         return response()->json(['message' => 'Duplicate order']);
//     }

//     // =========================
//     // CUSTOMER
//     // =========================
//     $billing = $data['billing'] ?? [];

//     $customer_name = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
//     $phone   = $billing['phone'] ?? '';
//     $email   = $billing['email'] ?? '';

//     $address = trim(($billing['address_1'] ?? '') . ' ' . ($billing['address_2'] ?? ''));
//     $city    = $billing['city'] ?? '';
//     $pincode = $billing['postcode'] ?? '';

//     $stateName   = $billing['state'] ?? '';
//     $countryName = $billing['country'] ?? '';

//     // =========================
//     // STATE + COUNTRY
//     // =========================
//     $state_id = null;
//     $country_id = null;

//     if (!empty($stateName)) {
//         $state_id = State::where('code', $stateName)->value('id')
//             ?? State::where('name', 'LIKE', "%$stateName%")->value('id');
//     }

//     if (!empty($countryName)) {
//         $country_id = DB::table('countries')->where('name', 'LIKE', "%$countryName%")->value('id');
//     }

//     // =========================
//     // CUSTOMER SAVE
//     // =========================
//     $customer = Customer::firstOrCreate(
//         ['mobile_no' => $phone],
//         [
//             'customer_name' => $customer_name,
//             'wp_number'     => $phone,
//             'vendor'        => "Customer",
//             'country_id'    => $country_id,
//             'email_id'      => $email,
//             'address'       => $address,
//             'state_id'      => $state_id,
//             'city_name'     => $city,
//             'pin_code'      => $pincode,
//         ]
//     );

//     $customer_id = $customer->id;

//     // =========================
//     // ORDER INSERT
//     // =========================
//     $nextBillNo = DB::table('sale_orderdetails')->max('Invoicenumber') + 1;

//     $saleOrderId = sale_details::insertGetId([
//         'billdate'      => date('Y-m-d'),
//         'user_id'       => "web",
//         'order_date'    => date('Y-m-d'),
//         'Invoicenumber' => $nextBillNo,
//         'totalproamt'   => $orderTotal,
//         'customer_name' => $customer_id,
//         'order_address' => $address,
//         'subtotal'      => $orderTotal,
//         'amt_pay'       => $orderTotal,
//         'Tamount'       => $orderTotal,
//         'import_no'     => $orderId,
//         'created_at'    => now(),
//         'updated_at'    => now(),
//     ]);

//     // =========================
//     // MERGE ITEMS
//     // =========================
//     $allItems = [];

//     if (!empty($data['line_items'])) {
//         $allItems = array_merge($allItems, $data['line_items']);
//     }

//     if (!empty($data['items'])) {
//         $allItems = array_merge($allItems, $data['items']);
//     }

//     // =========================
//     // LOOP ITEMS
//     // =========================
//     foreach ($allItems as $item) {

//         $productName = $item['name'] ?? $item['item_name'] ?? '';
//         $sku         = $item['sku'] ?? '';
//         $qty         = $item['quantity'] ?? 1;
//         $price       = $item['price'] ?? ($item['total'] ?? 0);

//         // =========================
//         // PARENT SKU (SAFE)
//         // =========================
//         $parentSku = $item['parent_sku'] ?? '';

//         if (empty($parentSku)) {
//             foreach ($data['line_items'] as $li) {
//                 if (($li['sku'] ?? '') === $sku) {
//                     $parentSku = $li['parent_sku'] ?? '';
//                     break;
//                 }
//             }
//         }

//         // =========================
//         // PRODUCT MATCH
//         // =========================
//         $productId = null;

//         if (!empty($parentSku)) {
//             $productId = Product::where('sku', $parentSku)->value('id');
//         }

//         if (!$productId && !empty($sku)) {
//             $productId = Product::where('sku', $sku)->value('id');
//         }

//         if (!$productId && !empty($productName)) {
//             $productId = Product::where('product_name', 'LIKE', "%$productName%")->value('id');
//         }

//         if (!$productId) {
//             file_put_contents($logFile, "❌ PRODUCT NOT FOUND: $productName\n", FILE_APPEND);
//             continue;
//         }

//         // =========================
//         // SIZE MATCH
//         // =========================
//         $sizeId = null;

//         if (!empty($sku)) {
//             $sizeId = Product_details::where('parentID', $productId)
//                 ->where('sku', $sku)
//                 ->value('id');
//         }

//         if (!$sizeId && !empty($item['meta_data'])) {
//             foreach ($item['meta_data'] as $meta) {
//                 if (strpos($meta['key'], 'pa_') === 0) {

//                     $variant = $meta['value'] ?? '';

//                     $sizeId = Product_details::where('parentID', $productId)
//                         ->where('product_size', 'LIKE', "%$variant%")
//                         ->value('id');

//                     break;
//                 }
//             }
//         }

//         // =========================
//         // INSERT ITEM
//         // =========================
//         sale_order::create([
//             'user_id'   => "web",
//             'pid'       => $saleOrderId,
//             'services'  => $productId,
//             'size'      => $sizeId,
//             'stage'     => "Raw",
//             'Quantity'  => $qty,
//             'qty'       => $qty,
//             'amount'    => $price * $qty,
//             'rate'      => $price,
//             'created_at'=> now(),
//             'updated_at'=> now(),
//         ]);

//         file_put_contents($logFile,
//             "✅ SAVED: $productName | ParentSKU: $parentSku | SKU: $sku\n",
//             FILE_APPEND
//         );
//     }

//     // =========================
//     // PAYMENT
//     // =========================
//     $nextPaymentNo = DB::table('purchase_payments')->max('ReceiptNo') + 1;

//     $paymentId = Sale_payment::insertGetId([
//         'ReceiptNo'     => $nextPaymentNo,
//         'user_id'       => "web",
//         'PurchaseDate'  => date('Y-m-d'),
//         'customer_name' => $customer_id,
//         'amt_pay'       => $orderTotal,
//         'totalvalue'    => $orderTotal,
//         'sale_id'       => $saleOrderId,
//         'date'          => date('Y-m-d'),
//         'created_at'    => now(),
//         'updated_at'    => now(),
//     ]);

//     Sale_paymentDetails::create([
//         'user_id'       => "web",
//         'pid'           => $paymentId,
//         'Invoicenumber' => $saleOrderId,
//         'amount'        => $orderTotal,
//         'payamt'        => $orderTotal,
//         'created_at'    => now(),
//         'updated_at'    => now(),
//     ]);

//     return response()->json([
//         'message' => 'Order saved successfully'
//     ]);
// }

// public function handleOrderee(Request $request)
// {
//     $logFile = __DIR__ . '/order_log.txt';

//     // 🔥 Confirm function hit
//     file_put_contents(__DIR__ . '/before.txt', "BEFORE TRY\n", FILE_APPEND);

//     try {

//         file_put_contents(__DIR__ . '/inside.txt', "INSIDE TRY\n", FILE_APPEND);

//         $payload = $request->getContent();
//         $data = json_decode($payload, true);

//         if (!$data) {
//             file_put_contents($logFile, "❌ Invalid JSON\n", FILE_APPEND);
//             return response()->json(['message' => 'Invalid data'], 400);
//         }

//         $orderId    = $data['id'] ?? '';
//         $orderTotal = $data['total'] ?? 0;

//         file_put_contents($logFile, "ORDER ID: $orderId\n", FILE_APPEND);

//         // =========================
//         // DUPLICATE CHECK
//         // =========================
//         if (\App\Models\sale_details::where('import_no', $orderId)->exists()) {
//             file_put_contents($logFile, "⚠️ DUPLICATE ORDER\n", FILE_APPEND);
//             return response()->json(['message' => 'Duplicate order']);
//         }

//         // =========================
//         // CUSTOMER
//         // =========================
//         $billing = $data['billing'] ?? [];

//         $customer_name = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
//         $phone   = $billing['phone'] ?? '';
//         $email   = $billing['email'] ?? '';

//         file_put_contents($logFile, "PHONE: $phone\n", FILE_APPEND);

//         // ✅ phone fallback
//         if (empty($phone)) {
//             $phone = 'NO_PHONE_' . time();
//         }

//         $customer = \App\Models\Customer::firstOrCreate(
//             ['mobile_no' => $phone],
//             [
//                 'customer_name' => $customer_name,
//                 'wp_number'     => $phone,
//                 'vendor'        => "Customer",
//                 'email_id'      => $email,
//             ]
//         );

//         file_put_contents($logFile, "✅ CUSTOMER ID: " . $customer->id . "\n", FILE_APPEND);

//         // =========================
//         // INVOICE FIX
//         // =========================
//         $nextBillNo = (\DB::table('sale_orderdetails')->max('Invoicenumber') ?? 0) + 1;

//         file_put_contents($logFile, "INVOICE NO: $nextBillNo\n", FILE_APPEND);

//         // =========================
//         // ORDER INSERT
//         // =========================
//         $saleOrderId = \App\Models\sale_details::insertGetId([
//             'billdate'      => date('Y-m-d'),
//             'user_id'       => "web",
//             'order_date'    => date('Y-m-d'),
//             'Invoicenumber' => $nextBillNo,
//             'totalproamt'   => $orderTotal,
//             'customer_name' => $customer->id,
//             'subtotal'      => $orderTotal,
//             'amt_pay'       => $orderTotal,
//             'Tamount'       => $orderTotal,
//             'import_no'     => $orderId,
//             'created_at'    => now(),
//             'updated_at'    => now(),
//         ]);

//         file_put_contents($logFile, "✅ ORDER ID: $saleOrderId\n", FILE_APPEND);

//         // =========================
//         // ITEMS
//         // =========================
//         $items = $data['line_items'] ?? [];

//         foreach ($items as $item) {

//             $name  = $item['name'] ?? '';
//             $qty   = $item['quantity'] ?? 1;
//             $price = $item['price'] ?? 0;

//             file_put_contents($logFile, "ITEM: $name\n", FILE_APPEND);

//             \App\Models\sale_order::create([
//                 'user_id'   => "web",
//                 'pid'       => $saleOrderId,
//                 'services'  => 1, // TEMP (avoid product error)
//                 'Quantity'  => $qty,
//                 'qty'       => $qty,
//                 'amount'    => $price * $qty,
//                 'rate'      => $price,
//                 'created_at'=> now(),
//                 'updated_at'=> now(),
//             ]);
//         }

//         file_put_contents($logFile, "✅ ITEMS SAVED\n", FILE_APPEND);

//         // =========================
//         // PAYMENT
//         // =========================
//         $nextPaymentNo = (\DB::table('purchase_payments')->max('ReceiptNo') ?? 0) + 1;

//         $paymentId = \App\Models\Sale_payment::insertGetId([
//             'ReceiptNo'     => $nextPaymentNo,
//             'user_id'       => "web",
//             'PurchaseDate'  => date('Y-m-d'),
//             'customer_name' => $customer->id,
//             'amt_pay'       => $orderTotal,
//             'totalvalue'    => $orderTotal,
//             'sale_id'       => $saleOrderId,
//             'date'          => date('Y-m-d'),
//             'created_at'    => now(),
//             'updated_at'    => now(),
//         ]);

//         file_put_contents($logFile, "✅ PAYMENT ID: $paymentId\n", FILE_APPEND);

//         return response()->json(['message' => 'SUCCESS']);

//     } catch (\Throwable $e) {

//         // 🔥 This will now ALWAYS catch error
//         file_put_contents(__DIR__ . '/error.txt',
//             "ERROR: " . $e->getMessage() . "\n" .
//             "LINE: " . $e->getLine() . "\n" .
//             "FILE: " . $e->getFile() . "\n\n",
//             FILE_APPEND
//         );

//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }


}
