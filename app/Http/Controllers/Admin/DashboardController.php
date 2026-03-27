<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\sale_details;
use Illuminate\Support\Facades\DB;
use App\Models\sale_order;
use App\Models\Product;
use App\Models\Product_details;
use App\Models\outward_details;
use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index ()
    {
        return view('admin.dashboard.index');
    }

public function setSeasonSession(Request $request)
{
    session()->forget('selected_season');
    session(['selected_season' => $request->season]);
    return response()->json(['success' => true]);
}

public function import()
    {
        return view('admin.ImportOrders.index');
    }




// public function importstore(Request $request)
// {
//     $request->validate([
//         'file' => 'required|mimes:xlsx,xls,csv',
//     ]);

//     $file = $request->file('file');

//     try {

//         $spreadsheet = IOFactory::load($file);
//         $sheet = $spreadsheet->getActiveSheet();
//         $data = $sheet->toArray();

//         $season = session('selected_season');

//         foreach ($data as $key => $row) {

//     if ($key == 0) continue; // skip header
//     $missingFields = [];

//         $rowNumber = $key + 1; // Excel row numbe
//                 if (!isset($row[9]) || empty($row[9])) continue;

//             $import_no = $row[0];
//             $address      = $row[5] ?? '';
//             $phone        = $row[9] ?? '';
//             $product_name = $row[17] ?? '';
//             $rawSize      = $row[18] ?? '';
//             $Tamount      = $row[16] ?? '';
//             // ✅ Check each field
//             if (empty($import_no)) {
//                 $missingFields[] = "Import No (Column 0)";
//             }
//             if (empty($address)) {
//                 $missingFields[] = "Address (Column 5)";
//             }
//             if (empty($phone)) {
//                 $missingFields[] = "Mobile (Column 9)";
//             }
//             if (empty($product_name)) {
//                 $missingFields[] = "Product (Column 17)";
//             }
//             if (empty($rawSize)) {
//                 $missingFields[] = "Size (Column 18)";
//             }
//             if (empty($Tamount)) {
//                 $missingFields[] = "Total Amount (Column 16)";
//             }

//             // ❌ If any missing → store error
//             if (!empty($missingFields)) {
//                 $errors[] = "Row {$rowNumber}: Missing -> " . implode(', ', $missingFields);
//                 continue;
//             }
//               // Check for duplicate import_no
//     $exists = DB::table('sale_orderdetails')->where('import_no', $import_no)->exists();

//     if ($exists) {
//         // Log::info('Skipping duplicate import_no: ' . $import_no);
//         continue;
//     }


//             if ($exists) continue;

//             $first_name  = $row[3] ?? '';
//             $last_name   = $row[4] ?? '';
//             $address     = $row[5] ?? '';
//             $city        = $row[6] ?? '';
//             $pincode     = $row[7] ?? '';
//             $email       = $row[8] ?? '';
//             $phone       = $row[9];
//             $stateName   = $row[11] ?? '';
//             $county       = $row[10] ?? '';
//             $customer_name = trim($first_name.' '.$last_name);

//             $state_id = State::where('name', 'LIKE', '%'.$stateName.'%')
//                             ->value('id');


//             $country_id = DB::table('countries')->where('name', 'LIKE', '%'.$county.'%')
//                             ->value('id');

//             $customer = Customer::where('mobile_no', $phone)->first();

//             if (!$customer) {
//                 $customer = Customer::create([
//                     'customer_name' => $customer_name,
//                     'mobile_no'     => $phone,
//                     'wp_number'     => $phone,
//                     'vendor'        => "Customer",
//                     'country_id'    => $country_id,
//                     'email_id'      => $email,
//                     'address'       => $address,
//                     'state_id'      => $state_id,
//                     'city_name'     => $city,
//                     'pin_code'      => $pincode,
//                 ]);
//             }

//             $customer_id = $customer->id;

//             /** ORDER */
//             $order_date = $row[2] ?? null;
//             $odate = $order_date ? date('Y-m-d', strtotime($order_date)) : date('Y-m-d');

//             $payment_method = $row[14] ?? '';
//             $Tamount        = $row[16] ?? 0;
//             $product_name   = $row[17] ?? '';
//             $rawSize        = $row[18] ?? '';
//             $qty            = $row[20] ?? 0;
//             $rate           = $row[22] ?? 0;

//             $product_size = trim(str_ireplace("size:", "", $rawSize));

//             $productId = Product::where('product_name', 'LIKE', '%'.$product_name.'%')
//                                 ->value('id');

//             $size = Product_details::where('parentID', $productId)
//                                    ->where('product_size', 'LIKE', '%'.$product_size.'%')
//                                    ->value('id');

//             // SAFE invoice number (instead of count)
//             $nextBillNo     = DB::table('sale_orderdetails')->max('Invoicenumber') + 1;
//             $nextOutwardNo  = DB::table('outward_details')->max('Invoicenumber') + 1;
//             $nextPaymentNo  = DB::table('purchase_payments')->max('ReceiptNo') + 1;

//             /** INSERT sale_orderdetails (Your sale_details) */
//             $saleOrderId = sale_details::insertGetId([
//                 'billdate'       => $odate,
//                 'user_id'        =>"web",
//                 'PurchaseDate'   => date('Y-m-d'),
//                 'order_date'     => date('Y-m-d'),
//                 'Invoicenumber'  => $nextBillNo,
//                 'batch_id'       => 1,
//                 'totalproamt'    => $Tamount,
//                 'customer_name'  => $customer_id,
//                 'order_address'  => $address,
//                 'dispatch'       => "no",
//                 'subtotal'       => $Tamount,
//                 'mode'           => $payment_method,
//                 'amt_pay'        => $Tamount,
//                 'Tamount'        => $Tamount,
//                 'season'         => $season,
//                 'import_no'      => $import_no,
//                 'created_at'     => now(),
//                 'updated_at'     => now(),
//             ]);

//             /** INSERT sale_order */
//             sale_order::create([
//                 'user_id'        =>"web",
//                 'pid'       => $saleOrderId,
//                 'services'  => $productId,
//                 'size'      => $size,
//                 'stage'     => "Raw",
//                 'Quantity'  => $qty,
//                 'qty'       => $qty,
//                 'amount'    => $rate * $qty,
//                 'rate'      => $rate,
//                 'update_id' => Auth::id(),
//                 'created_at'=> now(),
//                 'updated_at'=> now(),
//             ]);

//             /** OUTWARD */
//             // outward_details::create([
//             //     'user_id'         => Auth::id(),
//             //     'Invoicenumber'   => $nextOutwardNo,
//             //     'billdate'        => $odate,
//             //     'customer_name'   => $customer_id,
//             //     'order_no'        => $saleOrderId,
//             //     'services'        => $productId,
//             //     'size'            => $size,
//             //     'stage'           => "Raw",
//             //     'Quantity'        => $qty,
//             //     'qty'             => $qty,
//             //     'rem_qty'         => 0,
//             //     'currdispatch_qty'=> $qty,
//             //     'season'          => $season,
//             //     'update_id'       => Auth::id(),
//             //     'created_at'      => now(),
//             //     'updated_at'      => now(),
//             // ]);

//             /** PAYMENT */
//             $paymentId = Sale_payment::insertGetId([
//                 'ReceiptNo'     => $nextPaymentNo,
//                 'user_id'        =>"web",
//                 'user_id'        =>"web",
//                 'PurchaseDate'  => $odate,
//                 'customer_name' => $customer_id,
//                 'amt_pay'       => $Tamount,
//                 'totalvalue'    => $Tamount,
//                 'mode'          => $payment_method,
//                 'cheque_no'     => 0,
//                 'narration'     => 0,
//                 'sale_id'       => $saleOrderId,
//                 'season'        => $season,
//                 'date'          => $odate,
//                 'created_at'    => now(),
//                 'updated_at'    => now(),
//             ]);

//             Sale_paymentDetails::create([
//                 'user_id'        =>"web",
//                 'pid'           => $paymentId,
//                 'Invoicenumber' => $saleOrderId,
//                 'amount'        => $Tamount,
//                 'payamt'        => $Tamount,
//                 'created_at'    => now(),
//                 'updated_at'    => now(),
//             ]);
//         }

//     return redirect()->route('admin.orders.import')
//         ->with('success', 'Import completed with some skipped rows.')
//         ->with('warning', implode(', ', $errors));

//     } catch (\Exception $e) {

//         return back()->with('error', 'Error: '.$e->getMessage());

//     }
//     }



public function importstore(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    $file = $request->file('file');

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $season = session('selected_season');
        $errors = [];

        foreach ($data as $key => $row) {

            if ($key == 0) continue; // skip header

            $rowNumber = $key + 1;
            $missingFields = [];

            // ✅ Extract values
            $import_no   = trim($row[0] ?? null);
            $order_date  = $row[2] ?? null;
            $first_name  = trim($row[3] ?? '');
            $last_name   = trim($row[4] ?? '');
            $address     = trim($row[5] ?? '');
            $city        = trim($row[6] ?? '');
            $pincode     = trim($row[7] ?? '');
            $email       = trim($row[8] ?? '');
            $phone       = trim($row[9] ?? '');
            $countryName = trim($row[10] ?? '');
            $stateName   = trim($row[11] ?? '');
            $payment_method = trim($row[14] ?? '');
            $Tamount     = $row[16] ?? 0;
            $product_name= trim($row[17] ?? '');
            $rawSize     = trim($row[18] ?? '');
            $SizeSKU     = trim($row[19] ?? '');
            $qty         = $row[20] ?? 0;
            $rate        = $row[22] ?? 0;
            $ProductSKU  = trim($row[23] ?? '');

            if (empty($phone)) continue;

            $product_size = trim(str_ireplace("size:", "", $rawSize));

            // ✅ Basic validation
            if (empty($import_no))   $missingFields[] = "Import No";
            if (empty($address))     $missingFields[] = "Address";
            if (empty($product_name) && empty($ProductSKU)) $missingFields[] = "Product";
            if (empty($rawSize) && empty($SizeSKU)) $missingFields[] = "Size";
            if (empty($Tamount))     $missingFields[] = "Total Amount";

            // =========================
            // ✅ PRODUCT FIND (SKU → NAME)
            // =========================
            $productId = null;

            if (!empty($ProductSKU)) {
                $productId = Product::where('sku', $ProductSKU)->value('id');
            }

            if (!$productId && !empty($product_name)) {
                $productId = Product::where('product_name', 'LIKE', "%$product_name%")->value('id');
            }

            if (!$productId) {
                $missingFields[] = "Product not found (SKU/Name)";
            }

            // =========================
            // ✅ SIZE FIND (SKU → NAME)
            // =========================
            $size = null;

            if ($productId && !empty($SizeSKU)) {
                $size = Product_details::where('parentID', $productId)
                    ->where('sku', $SizeSKU)
                    ->value('id');
            }

            if (!$size && $productId && !empty($product_size)) {
                $size = Product_details::where('parentID', $productId)
                    ->where('product_size', 'LIKE', "%$product_size%")
                    ->value('id');
            }

            if (!$size) {
                $missingFields[] = "Size not found (SKU/Name)";
            }

            // ❌ Skip row if errors
            if (!empty($missingFields)) {
                $errors[] = "Row {$rowNumber}: " . implode(', ', $missingFields);
                continue;
            }

            // ✅ Duplicate check
            $exists = DB::table('sale_orderdetails')
                ->where('import_no', $import_no)
                ->exists();

            if ($exists) continue;

            // =========================
            // ✅ CUSTOMER
            // =========================
            $customer_name = trim($first_name . ' ' . $last_name);

            $state_id = State::where('name', 'LIKE', "%$stateName%")->value('id');
            $country_id = DB::table('countries')->where('name', 'LIKE', "%$countryName%")->value('id');

            $customer = Customer::firstOrCreate(
                ['mobile_no' => $phone],
                [
                    'customer_name' => $customer_name,
                    'wp_number'     => $phone,
                    'vendor'        => "Customer",
                    'country_id'    => $country_id,
                    'email_id'      => $email,
                    'address'       => $address,
                    'state_id'      => $state_id,
                    'city_name'     => $city,
                    'pin_code'      => $pincode,
                ]
            );

            $customer_id = $customer->id;

            // ✅ Date
            $odate = $order_date
                ? date('Y-m-d', strtotime($order_date))
                : date('Y-m-d');

            // ✅ Invoice numbers
            $nextBillNo    = DB::table('sale_orderdetails')->max('Invoicenumber') + 1;
            $nextPaymentNo = DB::table('purchase_payments')->max('ReceiptNo') + 1;

            // =========================
            // ✅ SALE ORDER DETAILS
            // =========================
            $saleOrderId = sale_details::insertGetId([
                'billdate'       => $odate,
                'user_id'        => "web",
                'PurchaseDate'   => date('Y-m-d'),
                'order_date'     => $odate,
                'Invoicenumber'  => $nextBillNo,
                'batch_id'       => 1,
                'totalproamt'    => $Tamount,
                'customer_name'  => $customer_id,
                'order_address'  => $address,
                'dispatch'       => "no",
                'subtotal'       => $Tamount,
                'mode'           => $payment_method,
                'amt_pay'        => $Tamount,
                'Tamount'        => $Tamount,
                'season'         => $season,
                'import_no'      => $import_no,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // =========================
            // ✅ SALE ORDER
            // =========================
            sale_order::create([
                'user_id'   => "web",
                'pid'       => $saleOrderId,
                'services'  => $productId,
                'size'      => $size,
                'stage'     => "Raw",
                'Quantity'  => $qty,
                'qty'       => $qty,
                'amount'    => $rate * $qty,
                'rate'      => $rate,
                'update_id' => Auth::id(),
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);

            // =========================
            // ✅ PAYMENT
            // =========================
            $paymentId = Sale_payment::insertGetId([
                'ReceiptNo'     => $nextPaymentNo,
                'user_id'       => "web",
                'PurchaseDate'  => $odate,
                'customer_name' => $customer_id,
                'amt_pay'       => $Tamount,
                'totalvalue'    => $Tamount,
                'mode'          => $payment_method,
                'sale_id'       => $saleOrderId,
                'season'        => $season,
                'date'          => $odate,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            Sale_paymentDetails::create([
                'user_id'        => "web",
                'pid'            => $paymentId,
                'Invoicenumber'  => $saleOrderId,
                'amount'         => $Tamount,
                'payamt'         => $Tamount,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return redirect()->route('admin.orders.import')
            ->with('success', 'Import completed')
            ->with('warning', implode(' | ', $errors));

    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}
