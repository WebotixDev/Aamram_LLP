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

        foreach ($data as $key => $row) {
            if ($key == 0) continue; // header
            if (!isset($row[9]) || empty($row[9])) continue;

            $import_no = $row[0];

              // Check for duplicate import_no
    $exists = DB::table('sale_orderdetails')->where('import_no', $import_no)->exists();

    if ($exists) {
        // Log::info('Skipping duplicate import_no: ' . $import_no);
        continue;
    }


            if ($exists) continue;

            $first_name  = $row[3] ?? '';
            $last_name   = $row[4] ?? '';
            $address     = $row[5] ?? '';
            $city        = $row[6] ?? '';
            $pincode     = $row[7] ?? '';
            $email       = $row[8] ?? '';
            $phone       = $row[9];
            $stateName   = $row[11] ?? '';
            $county       = $row[10] ?? '';
            $customer_name = trim($first_name.' '.$last_name);

            $state_id = State::where('name', 'LIKE', '%'.$stateName.'%')
                            ->value('id');


            $country_id = DB::table('countries')->where('name', 'LIKE', '%'.$county.'%')
                            ->value('id');

            $customer = Customer::where('mobile_no', $phone)->first();

            if (!$customer) {
                $customer = Customer::create([
                    'customer_name' => $customer_name,
                    'mobile_no'     => $phone,
                    'wp_number'     => $phone,
                    'vendor'        => "Customer",
                    'country_id'    => $country_id,
                    'email_id'      => $email,
                    'address'       => $address,
                    'state_id'      => $state_id,
                    'city_name'     => $city,
                    'pin_code'      => $pincode,
                ]);
            }

            $customer_id = $customer->id;

            /** ORDER */
            $order_date = $row[2] ?? null;
            $odate = $order_date ? date('Y-m-d', strtotime($order_date)) : date('Y-m-d');

            $payment_method = $row[14] ?? '';
            $Tamount        = $row[16] ?? 0;
            $product_name   = $row[17] ?? '';
            $rawSize        = $row[18] ?? '';
            $qty            = $row[20] ?? 0;
            $rate           = $row[22] ?? 0;

            $product_size = trim(str_ireplace("size:", "", $rawSize));

            $productId = Product::where('product_name', 'LIKE', '%'.$product_name.'%')
                                ->value('id');

            $size = Product_details::where('parentID', $productId)
                                   ->where('product_size', 'LIKE', '%'.$product_size.'%')
                                   ->value('id');

            // SAFE invoice number (instead of count)
            $nextBillNo     = DB::table('sale_orderdetails')->max('Invoicenumber') + 1;
            $nextOutwardNo  = DB::table('outward_details')->max('Invoicenumber') + 1;
            $nextPaymentNo  = DB::table('purchase_payments')->max('ReceiptNo') + 1;

            /** INSERT sale_orderdetails (Your sale_details) */
            $saleOrderId = sale_details::insertGetId([
                'user_id'        => Auth::id(),
                'billdate'       => $odate,
                'PurchaseDate'   => date('Y-m-d'),
                'order_date'     => date('Y-m-d'),
                'Invoicenumber'  => $nextBillNo,
                'batch_id'       => 1,
                'totalproamt'    => $Tamount,
                'customer_name'  => $customer_id,
                'order_address'  => $address,
                'dispatch'       => "yes",
                'subtotal'       => $Tamount,
                'mode'           => $payment_method,
                'amt_pay'        => $Tamount,
                'Tamount'        => $Tamount,
                'season'         => $season,
                'import_no'      => $import_no,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            /** INSERT sale_order */
            sale_order::create([
                'user_id'   => Auth::id(),
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

            /** OUTWARD */
            outward_details::create([
                'user_id'         => Auth::id(),
                'Invoicenumber'   => $nextOutwardNo,
                'billdate'        => $odate,
                'customer_name'   => $customer_id,
                'order_no'        => $saleOrderId,
                'services'        => $productId,
                'size'            => $size,
                'stage'           => "Raw",
                'Quantity'        => $qty,
                'qty'             => $qty,
                'rem_qty'         => 0,
                'currdispatch_qty'=> $qty,
                'season'          => $season,
                'update_id'       => Auth::id(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            /** PAYMENT */
            $paymentId = Sale_payment::insertGetId([
                'ReceiptNo'     => $nextPaymentNo,
                'user_id'       => Auth::id(),
                'updateduser'   => Auth::id(),
                'PurchaseDate'  => $odate,
                'customer_name' => $customer_id,
                'amt_pay'       => $Tamount,
                'totalvalue'    => $Tamount,
                'mode'          => $payment_method,
                'cheque_no'     => 0,
                'narration'     => 0,
                'sale_id'       => $saleOrderId,
                'season'        => $season,
                'date'          => $odate,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            Sale_paymentDetails::create([
                'pid'           => $paymentId,
                'Invoicenumber' => $saleOrderId,
                'amount'        => $Tamount,
                'payamt'        => $Tamount,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return redirect()->route('admin.orders.import')
            ->with('success', 'Imported successfully!');

    } catch (\Exception $e) {

        return back()->with('error', 'Error: '.$e->getMessage());
        
    }
    }
}