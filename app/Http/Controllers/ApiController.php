<?php
namespace App\Http\Controllers;
use App\Models\inward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;




class ApiController extends Controller
{


  public function getProducts()
   {
       
   $products = DB::table('products')
    ->where('status', 1)
    ->get();


    // Prepare the response data
    $response = [];

    foreach ($products as $product) {
        // Fetch product details using product ID (pid in product_details)
        $product_details = DB::table('product_details')
            ->where('parentID', $product->id)
            ->where('status', 1)
            ->get();

        // Prepare details array
        $details = [];
        foreach ($product_details as $detail) {
            $details[] = [
                'parentID' => $detail->parentID,
                'id'    => $detail->id,
                'product_size' => $detail->product_size,
                'web_price' => $detail->web_price, // Assuming a description field
                'discount' => $detail->discount, // Assuming a price field
                'original_price' => $detail->original_price, // Assuming a price field
                'status'    =>$detail->status,
            ];
        }

        // Add product with its details to response
        $response[] = [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'status' => $product->status,
            'img' => $product->img,
            'description' => $product->description,
            'cgst' => $product->cgst,
            'sgst' => $product->sgst,
            'igst' => $product->igst,
            'details' => $details,
        ];
    }

    return response()->json($response);
}


public function insertCustomer(Request $request)
{
    // Get mobile number from request
  $mobileNumber = $request->input('mobile_no');

   // Check if customer already exists
   $existingCustomer = DB::table('customers')->where('mobile_no', $mobileNumber)->first();

   if ($existingCustomer) {
       return response()->json([
           'success' => false,
           'customer_id'=>$existingCustomer->id,
           'message' => 'Customer with this mobile number already exists'
       ], 400);
   }
    // Prepare data for insertion
    $customerData = [
        'customer_name' => $request->input('customer_name'),
        'company_name'  => $request->input('company_name'),
        'mobile_no'     => $mobileNumber,
        'wp_number'     => $mobileNumber,
        'vendor'        => $request->input('vendor'),
        'email_id'      => $request->input('email_id'),
        'address' => $request->input('address') . ', ' . $request->input('city_name') . ', ' . $request->input('pin_code'),
        'city_name'     => $request->input('city_name'),
        'pin_code'      => $request->input('pin_code'),
        'country_id'    => $request->input('country_id'),
        'state_id'      => $request->input('state_id'),
        'district_id'   => $request->input('district_id'),
        'created_at'    => now(),
        'updated_at'    => now(),
    ];

    // Insert customer and get ID
    $customerId = DB::table('customers')->insertGetId($customerData);

    // Return success response
    return response()->json([
        'success' => true,
        'id'      => $customerId,
        'message' => 'Customer inserted successfully'
    ]);
}



    public function updateCustomer(Request $request)
{
    // Extract the customer ID from the request
    $id = $request->input('id');

    if (!$id) {
        return response()->json(['success' => false, 'message' => 'Customer ID is required'], 400);
    }


    // Build the array of values to update
    $arrValue = [
        'customer_name' => $request->input('customer_name'),
        'company_name'  => $request->input('company_name'),
        'mobile_no'     => $request->input('mobile_no'),
        'vendor'        => $request->input('vendor'),
        'email_id'      => $request->input('email_id'),
        'address' => $request->input('address') . ', ' . $request->input('city_name') . ', ' . $request->input('pin_code'),
        'city_name'     => $request->input('city_name'),
        'pin_code'      => $request->input('pin_code'),
        'country_id'    => $request->input('country_id'),
        'state_id'      => $request->input('state_id'),
        'district_id'   => $request->input('district_id'),
        'updated_at'    => now(),
    ];

    try {
        // Attempt to update the customer record
        $updated = DB::table('customers')
            ->where('id', $id)
            ->update($arrValue);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'No changes made or customer not found']);
        }
    } catch (\Exception $e) {
        // Optionally log the error: Log::error($e);
        return response()->json([
            'success' => false,
            'message' => 'Error updating customer',
            'error'   => $e->getMessage()
        ], 500);
    }
}




    public function getCountries()
    {
        // Fetch all countries from the database
        $countries = DB::table('countries')->get();

        // Return the data as JSON response
        return response()->json($countries);
    }

    public function getStates()
    {
        // Fetch all countries from the database
        $states = DB::table('states')->get();

        // Return the data as JSON response
        return response()->json($states);
    }

    public function getDistrics()
    {
        // Fetch all countries from the database
        $districts = DB::table('districts')->get();

        // Return the data as JSON response
        return response()->json($districts);
    }


    public function getCities()
    {
        // Fetch all countries from the database
        $cities = DB::table('cities')->get();

        // Return the data as JSON response
        return response()->json($cities);
    }


    public function CustomerData()
    {
        // Fetch all countries from the database
        $customers = DB::table('customers')->get();

        // Return the data as JSON response
        return response()->json($customers);
    }


    public function getstockbatch(Request $request)
{
   
    $ID = $request->query('ID');
    $size = $request->query('size');
  
   $getsizeid = DB::table('product_details')
   ->where('parentID', $ID)
   ->where('id',$size)
   ->value('product_size');

    if (strpos($getsizeid, "Full") !== false) {
        $stage = "Raw";
        }
        else{
        $stage = "Semi Ripe";
        }
    

    //$stage = $request->query('stage');
    $Date = $request->query('Date');

    if (!$ID || !$size || !$stage || !$Date) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    $pquantity = DB::table('purchase_product')
        ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
        ->where('purchase_product.services', $ID)
        ->where('purchase_product.size', $size)
        ->where('purchase_product.stage', $stage)
        ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
        ->sum('purchase_product.Quantity');

    $squantity = DB::table('sale_order')
        ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
        ->where('sale_order.services', $ID)
        ->where('sale_order.size', $size)
        ->where('sale_order.stage', $stage)
        ->whereDate('sale_orderdetails.order_date', '<=', $Date)
        ->sum('sale_order.qty');
        
        //$pquantity - $squantity
        
        $qty=100;


        return response()->json([
            'stock_balance' => $qty,
            'stage' => $stage
        ]);

}


    public function saleorderData()
    {
        $sale_orderdetails = DB::table('sale_orderdetails')->get();

        $sale_orders = [];

        foreach ($sale_orderdetails as $sale_orderdetail) {

            $sale_order = DB::table('sale_order')->where('pid', $sale_orderdetail->id)->get();
            $sale_orders[] = $sale_order;
        }

        return response()->json([
            'sale_orderdetails' => $sale_orderdetails,
            'sale_orders' => $sale_orders
        ]);
    }


    function insertSaleorder(Request $request)
    {


     if($request->amt_pay != 0  || $request->amt_pay != ""){

        $countinv = DB::table('sale_orderdetails')->count();

        $Customeraddress = DB::table('customers')->where('id', $request->customer_name_sale)->first();

        $nextInvNo = $countinv > 0 ? $countinv + 1 : 1;

        $arrValue = [

                'user_id'     => $request->user_id,
                'dispatch'=> 'yes',
                'mode'  => "Epayment",
                'totalproamt' => $request->amt_pay,
                'amt_pay' => $request->amt_pay,
                'PurchaseDate'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'billdate'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'order_date'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'Invoicenumber'  => $nextInvNo,
                'batch_id'       => 1,
                'customer_name'  => $request->customer_name_sale,//r
                 'order_address' => $Customeraddress->address,

                'Tamount'        => $request->amt_pay,//r
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),

        ];

           // Insert sale order record and get the inserted ID
           $saleOrderId = DB::table('sale_orderdetails')->insertGetId($arrValue);


 /////// /////////////////////////////////////Distribute mangoes function//////////////////////////////////////

            // function distributeMangoes($orderQuantity, $productId,$size, $stage, $saleOrderId) {
            //     // Fetch batch details for the specific product
            //     $batchDetails = DB::table('purchase_product as pp')
            //         ->join('purchase_details as pd', 'pd.id', '=', 'pp.pid')
            //         ->where('pp.services', $productId)
            //         ->where('pp.size', $size)
            //         ->where('pp.stage', $stage)
            //         ->where('pp.complete_flag', 0)
            //         ->select('pd.batch_id', 'pp.quantity', 'pp.services', 'pd.id as purchase_detail_id')
            //         ->orderBy('pd.created_at', 'asc') // Ensures batches are retrieved in ascending order
            //         ->get();


            //     $remainingQuantity = $orderQuantity;

            //     foreach ($batchDetails as $batch) {
            //         if ($remainingQuantity <= 0) {
            //             break; // Stop if all required quantity is fulfilled
            //         }

            //         // Calculate the quantity to deduct
            //         $deductQuantity = min($remainingQuantity, $batch->quantity);
            //         $remainingQuantity -= $deductQuantity;

            //         $newQuantity = $batch->quantity - $deductQuantity;

            //         // Insert into batch history instead of updating purchase_product
            //         $ocupprdbatch=DB::table('batch_history')->insert([
            //             'batch_id'       => $batch->batch_id,
            //             'orderid'        => $saleOrderId,
            //             'deducted_qty'   => $deductQuantity,
            //             'productid'      => $productId,
            //             'sizeid'         => $size,
            //             'stageid'        => $stage,
            //             'rem_qty'        => $newQuantity,
            //             'created_at'     => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            //         ]);
            //     }


            //     // Handle case where not enough quantity is available
            //     if ($remainingQuantity > 0) {
            //         throw new Exception("Insufficient quantity available across batches.");
            //     }

            //     // Check and update the complete_flag for affected batches based on batch_history
            //     $affectedBatchIds = $batchDetails->pluck('batch_id')->unique();

            //     foreach ($affectedBatchIds as $batchId) {
            //         // Check the total remaining quantity in the batch using batch_history
            //         $totalRemainingQuantity = DB::table('batch_history')
            //             ->where('batch_id', $batchId)
            //             ->where('productid', $productId)
            //             ->where('sizeid', $size)
            //             ->where('stageid', $stage)
            //             ->sum('rem_qty');

            //         // If no products remain in the batch, mark it as complete
            //         if ($totalRemainingQuantity == 0) {
            //             DB::table('purchase_product')
            //                 ->where('batch_id', $batchId)
            //                 ->where('services', $productId)
            //                 ->where('size', $size)
            //                 ->where('stage', $stage)
            //                 ->update([
            //                     'complete_flag' => 1,
            //                     'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            //                 ]);
            //         }
            //     }
            // }

////////////////////////////////////////end function////////////////////////////////////

                    $services = explode(',', $request->services ?? '');
                    $sizes = explode(',', $request->size ?? '');
                    $stages = explode(',', $request->stage ?? '');
                    $rates = explode(',', $request->rate ?? '');
                    $quantities = explode(',', $request->qty ?? '');
                    $amt = explode(',', $request->total_price ?? '');


                    foreach ($services as $index => $service) {
                        $size = $sizes[$index] ?? null;
                        $stage = $stages[$index] ?? null;
                        $rate = $rates[$index] ?? null;
                        $qty = $quantities[$index] ?? null;
                        $amts = $amt[$index] ?? null;


                        //distributeMangoes($qty, $service, $size, $stage, $saleOrderId);

                        $saleOrderDetailData = [
                            'user_id'     => $request->user_id,
                            'pid'         => $saleOrderId,
                            'services'    => $service,
                            'size'        => $size,
                            'stage'       => $stage,
                            'qty'         => $qty,
                            'Quantity'    => 0,
                            'amount'      => $amts,
                            'rate'        => $rate,
                            'created_at'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        ];

                        DB::table('sale_order')->insert($saleOrderDetailData);

                        $count = DB::table('outward_details')->count();

                     // Determine the next Bill_No based on existing records
                     
                     $nextBillNo = $count > 0 ? $count + 1 : 1;
                        $outwardData = [
                          'user_id'     => $request->user_id,
                            'Invoicenumber'   => $nextBillNo,
                            'billdate'        => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                            'customer_name'   => $request->customer_name_sale,
                            'order_no'        => $saleOrderId,
                            'services'        => $service,
                            'size'            => $size,
                            'stage'           => $stage,
                            'Quantity'        => $qty,
                            'qty'             => $qty,
                            'dispatch'        => $qty,
                            'rem_qty'         => 0,
                            'currdispatch_qty'=> $qty,
                            'created_at'      => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'updated_at'      => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'update_id'       => 'web',
                        ];

                        DB::table('outward_details')->insert($outwardData);
                    
                    
                    }



                     $count = DB::table('purchase_payments')->count();

                     // Determine the next Bill_No based on existing records
                     $nextBillNo = $count > 0 ? $count + 1 : 1;

                     $salePaymnetData = [

                       'user_id'     => $request->user_id,
                         'ReceiptNo'      => $nextBillNo,
                         'PurchaseDate'    =>  now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                         'customer_name'  => $request->customer_name_sale,
                         'amt_pay'         => $request->amt_pay,
                         'totalvalue'     => $request->amt_pay,
                         'mode'  => "Epayment",
                         'date'           =>now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                         'created_at'      => now()->format('Y-m-d'),
                     ];

                     $paymentinfo = DB::table('purchase_payments')->insertGetId($salePaymnetData);

                     $payamt=$request->amt_pay - $request->amt_pay;

                     $salePaymentDetailData = [

                       'user_id'     => $request->user_id,
                         'pid'           => $paymentinfo, // assuming $salePaymentId is available
                         'Invoicenumber' => $saleOrderId,
                         'amount'         => $request->amt_pay,
                         'payamt'        => $payamt,
                         'created_at'     => now()->format('Y-m-d'),
                         'updated_at'     => now()->format('Y-m-d'),
                     ];

                     // Insert sale order detail record
                     $paymentinfoforcust= DB::table('purchase_payment_info')->insert($salePaymentDetailData);

                    $razorpaydata = [
                       'user_id'     => $request->user_id,
                        'transaction_id' => $request->transaction_id,
                        'order_id' => $saleOrderId,
                        'signature' => 'NULL',
                        'paid_amount' => $request->amt_pay,
                        'customer_name' => $request->customer_name_sale,
                        'created_at' => now(),
                    ];

                    $outwardDatas= DB::table('razorpay_history')->insert($razorpaydata);
            
    // echo "$outwardDatas";
    if($outwardDatas){
 
            
                // $servicesss = explode(',', $request->services ?? '');
                // $sizesss = explode(',', $request->size ?? '');
                // $billlink =" https://inventory.aamrammango.com/sale-order/{$saleOrderId}/print";
                
            
                // $results = [];
            
                // foreach ($servicesss as $service) {
                //     $query = DB::table('products as p')
                //         ->join('product_details as pd', 'p.id', '=', 'pd.parentID')
                //         ->where('p.id', $service)
                //         ->whereIn('pd.id', $sizesss)
                //         ->select('p.product_name', 'pd.product_size')
                //         ->get();
            
                //     $results = array_merge($results, $query->toArray());
                // }
            
                // $string = "";
            
                // foreach ($results as $item) {
                //     if (is_object($item)) {
                //         $string .= $item->product_name . " - " . $item->product_size . ", ";
                //     } else {
                //         $string .= $item . ", ";
                //     }
                // }
            
                // $string = rtrim($string, ", "); // Remove trailing comma
                
                $servicesss = explode(',', $request->services ?? '');
                $sizesss = explode(',', $request->size ?? '');
                $stagess = explode(',', $request->stage ?? '');
                $qtyss = explode(',', $request->qty ?? '');
                
                $string = "*Product Details:* "; // Optional: Use asterisk for bold in WhatsApp
                
                foreach ($servicesss as $index => $service) {
                $product = DB::table('products as p')
                ->join('product_details as pd', 'p.id', '=', 'pd.parentID')
                ->where('p.id', $service)
                ->where('pd.id', $sizesss[$index] ?? null)
                ->select('p.product_name', 'pd.product_size')
                ->first();
                
                $productName = $product->product_name ?? 'N/A';
                $productSize = $product->product_size ?? 'N/A';
                $stage = $stagess[$index] ?? 'N/A';
                $qty = "*Qty-" . ($qtyss[$index] ?? 'N/A') . "*";
                
                $srNo = $index + 1;
                $string .= "$srNo) $productName | $productSize | $stage | $qty ";
                }

                
              $amt="*".$request->amt_pay."*";
             
              $billlink ="https://inventory.aamrammango.com/sale-order/{$saleOrderId}/print";
                
                
              $existingCustomer = DB::table('customers')->where('id', $request->customer_name_sale)->first();
            
                if ($existingCustomer) {
                $name = $existingCustomer->customer_name;
                $mobnumber = $existingCustomer->mobile_no;
                $caddress = $existingCustomer->address;
                
                
                // If mobile_no is not already prefixed with '91' and not 12 digits, prepend '91'
                if (!(strlen($mobnumber) === 12 && substr($mobnumber, 0, 2) === '91')) {
                $mobnumber = '91' . $mobnumber;
                }
                
                $namemob = "*".$existingCustomer->customer_name."-".$mobnumber."*";
                
                
                } else {
                // Handle case where customer not found
                return response()->json(['success' => false, 'message' => 'Customer not found']);
                }
                
                 $mobnumber1=917768944440;
                 $address="*$caddress*";
                 $type=$request->user_id;
               
            
                $curl = curl_init();
            
                curl_setopt_array($curl, [
                    CURLOPT_URL            => 'https://backend.aisensy.com/campaign/t1/api/v2',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => json_encode([
                        "apiKey"         => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY3YmVhYTYzOGUwZjVmMGMxYTliOTI0MiIsIm5hbWUiOiJBYW1yYW0gTWFuZ28gMiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2N2E5ZGU5YmVmMGQ4ZjBjMGRkOGM5NjciLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzQwNTQ4NzA3fQ.YyT1y2n5MMvcA4yVZZ6AoNLmAliEwMcRcUCn-3Ym8ak",
                        "campaignName"   => "webotixsaleorderchatbot",
                        "destination"    => "$mobnumber",
                        "userName"       => "Aamram Mango 2",
                        "templateParams" => [$name, $string, $amt,$billlink],
                        "source"         => "new-landing-page form"
                    ]),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
            
                    $response = curl_exec($curl);
                
                curl_close($curl);
                
             ////////////////////////////////////////////////////////////////////////////////////////   
                
               // echo "<br>";
                
                 $curl1 = curl_init();
            
                curl_setopt_array($curl1, [
                    CURLOPT_URL            => 'https://backend.aisensy.com/campaign/t1/api/v2',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => json_encode([
                        "apiKey"         => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY3YmVhYTYzOGUwZjVmMGMxYTliOTI0MiIsIm5hbWUiOiJBYW1yYW0gTWFuZ28gMiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2N2E5ZGU5YmVmMGQ4ZjBjMGRkOGM5NjciLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzQwNTQ4NzA3fQ.YyT1y2n5MMvcA4yVZZ6AoNLmAliEwMcRcUCn-3Ym8ak",
                        "campaignName"   => "saleorderowner",
                        "destination"    => "$mobnumber1",
                        "userName"       => "Aamram Mango 2",
                        "templateParams" => [$namemob,$address,$string,$type],
                        "source"         => "new-landing-page form"
                    ]),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
            
                    $response1 = curl_exec($curl1);
                
                curl_close($curl1);
                
                
            
              
        return response()->json(['success' => true]);
       // die;
        
     }}else{
         
           return response()->json(['success' => false]);

     }

    }
    
    
    
    // public function checkMobile(Request $request)
    // {
    //     // Get mobile number from request
    //     $number = $request->input('mobile_no');

    //     // Check if the mobile number exists in the 'customers' table
    //     $customer = DB::table('customers')->where('mobile_no', $number)->first();

    //     if ($customer) {
    //         // If mobile number exists, generate OTP
    //         //$otp = rand(100000, 999999); // Generate a random 6-digit OTP

    //         $otp = '1234';
    //         // Insert the OTP into the customers table
    //         //DB::table('customers')->where('mobile_no', $number)->update(['otp' => $otp]);

    //         // Send OTP (you can use a service like Twilio to send an SMS here)

    //         // For demonstration, we'll just return the OTP in the response
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'OTP sent successfully.',
    //             'otp' => $otp, // You may remove this in production
    //         ]);
    //     } else {
    //         // If mobile number does not exist, return error
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Mobile number not found.',
    //         ], 404);
    //     }
    // }




 public function checkMobile(Request $request)
    {
        // Get mobile number from request
        $number = $request->input('mobile_no');

        // Check if the mobile number exists in the 'customers' table
        $customer = DB::table('customers')->where('mobile_no', $number)->first();

        if ($customer) {

            // If mobile number exists, generate OTP
            $otp = rand(100000, 999999); // Generate a random 6-digit OTP

            DB::table('customers')->where('mobile_no', $number)->update(['otp' => $otp]);

            $mobnumber="91".$number;

if($mobnumber){
                $url = "https://backend.aisensy.com/campaign/t1/api/v2";
                
                $data = [
                "apiKey" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY3YmVhYTYzOGUwZjVmMGMxYTliOTI0MiIsIm5hbWUiOiJBYW1yYW0gTWFuZ28gMiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2N2E5ZGU5YmVmMGQ4ZjBjMGRkOGM5NjciLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzQwNTQ4NzA3fQ.YyT1y2n5MMvcA4yVZZ6AoNLmAliEwMcRcUCn-3Ym8ak",
                "campaignName" => "webotixverify",
                "destination" => "$mobnumber",
                "userName" => "Aamram Mango 2",
                "templateParams" => ["$otp"],
                "source" => "new-landing-page form",
                "buttons" => [
                [
                "type" => "button",
                "sub_type" => "url",
                "index" => 0,
                "parameters" => [
                [
                    "type" => "text",
                    "text" => "TESTCODE20"
                ]
                ]
                ]
                ],
                "carouselCards" => [],
                "paramsFallbackValue" => [
                "FirstName" => "user"
                ]
                ];
                
                $payload = json_encode($data);
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                
                $response = curl_exec($ch);
                curl_close($ch);
                
}

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully.',
                'otp' => $otp, // You may remove this in production
            ]);
        } else {
            // If mobile number does not exist, return error
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number not found.',
            ], 404);
        }
    }
    
    

    public function verifyOtp(Request $request)
{
    // Get mobile number and OTP from request
    $mobileNo = $request->input('mobile_no');
    $otp = $request->input('otp');

    // Check if the customer exists with the provided mobile number
    $customer = DB::table('customers')->where('mobile_no', $mobileNo)->first();

    // Check if the customer exists and OTP matches
    if ($customer && $customer->otp == $otp) {
        // OTP is correct, return success response
        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.',
            'customer_id' => $customer->id,  // Return the customer ID

        ]);
    } else {
        // OTP is incorrect, return error response
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid OTP or mobile number.',
        ], 400);
    }
}


public function chatbotstock(Request $request)
{
   
    // $requstlink = request()->getRequestUri();

    // if ($requstlink) {
    //     $requstlink = str_replace('+', '%2B', $requstlink);
    //     $urlParts = parse_url($requstlink);
    //     if (isset($urlParts['query'])) {
    //         parse_str($urlParts['query'], $queryParams);
    //         //print_r($queryParams);
    //     }
    // }
    
    
    // $cname = $queryParams['cname'];
    // $mob = $queryParams['mob'];
    // $proname = $queryParams['proname'];
    // $prosize = $queryParams['prosize'];
    // $qty = $queryParams['qty'];
    //$type = $queryParams['type'];
    
     $cname = $request->cname;
     $mob = $request->mob;
     $proname = $request->proname;
     $prosize = $request->prosize;
     $qty = $request->qty;

   
if (strpos($prosize, "Full") !== false) {
    $type = "Raw";
}
else{
    $type = "Semi Ripe";

}


     $date = date("Y-m-d");

    // if (!$proname || !$prosize || !$type || !$qty) {
    //     return response()->json(['error' => 'Missing required parameters'], 400);
    // }

    // $getproid = DB::table('products')
    //     ->where('product_name', 'like', "%$proname%")
    //     ->value('id');

      
    // if (!$getproid) {
    //     return response()->json(['error' => 'Product not found'], 404);
    // }

    // $getsizeid = DB::table('product_details')
    //     ->where('parentID', $getproid)
    //     ->where('product_size', 'like', "%$prosize%")
    //     ->value('id');

      
    //     // echo $prosize; // Output the size
    //     // die;


    // if (!$getsizeid) {
    //     return response()->json(['error' => 'Product size not found'], 404);
    // }
    

    // $pquantity = DB::table('purchase_product')
    //     ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
    //     ->where('purchase_product.services', $getproid)
    //     ->where('purchase_product.size', $getsizeid)
    //     ->where('purchase_product.stage', $type)
    //     ->whereDate('purchase_details.PurchaseDate', '<=', $date)
    //     ->sum('purchase_product.Quantity');

    // $squantity = DB::table('sale_order')
    //     ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
    //     ->where('sale_order.services', $getproid)
    //     ->where('sale_order.size', $getsizeid)
    //     ->where('sale_order.stage', $type)
    //     ->whereDate('sale_orderdetails.order_date', '<=', $date)
    //     ->sum('sale_order.qty');



    // $currentstock = $pquantity - $squantity;

    // echo $currentstock;

    // die;

    //if ($currentstock >= $qty) {
        $params = [
            'cname' => $cname,
            'mob' => $mob,
            'proname' => $proname,
            'prosize' => $prosize,
            'qty' => $qty,
            'type' => $type,
            'date' => $date
        ];
    
        // Generate URL with query parameters
        $baseUrl = "https://aamrammango.com/chatbot.php"; // Change '/payment' to your actual route
        $queryString = http_build_query($params);
        $paymentLink = $baseUrl . '?' . $queryString;
    
        $message = "Thank you for your order of {$proname} {$prosize}. Please click the below link to make payment & confirm your order: $paymentLink";
    
        return response()->json(['available' => true, 'link' => $message], 200, [], JSON_UNESCAPED_SLASHES);
    //}
    
    

    // If stock is not available, return false
    //return response()->json(['available' => false]);



}
}
