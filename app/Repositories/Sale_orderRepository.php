<?php

namespace App\Repositories;

use App\Models\sale_order;
use App\Models\sale_details;
use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\outward_details;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class Sale_orderRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return sale_order::class;
    }

    /**
     * Store a new sale order along with its details in the database.
     */
    public function store(Request $request)
    {

        $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');

    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
        DB::beginTransaction();
        try {
            $saleOrderData = [
                'user_id'        => Auth::id(),
                'billdate'       => $billDate,
                'PurchaseDate'  => $billDate,
                'order_date'  => $billDate,
                'Invoicenumber'  => $request->Invoicenumber,
                'batch_id'       => 1,
                'totalproamt'=>  $request->totalproamt,
                'customer_name'  => $request->customer_name_sale,
                'order_address'  => $request->order_address,
                'wholesaler'     =>$request->wholesaler,
                'gst'  => $request->gst,
                'dispatch' => $request->dispatch,
                'discount_per'   => $request->discount_per,
                'discount_rupee' => $request->discount_rupee,
                'subtotal' => $request->subtotal,
                'trans_cost'     => $request->trans_cost,
                'trans_in_per'     => $request->trans_in_per,
                'trans_in_per_rs'     => $request->trans_in_per_rs,
                'other_charges'   => $request->other_charges,
                'CGST' => $request->CGST,
                'SGST' => $request->SGST,
                'IGST'     => $request->IGST,
                'mode'     => $request->mode,
                'amt_pay'     => $request->amt_pay,
                'narration'     => $request->narration,
                'Tamount'        => $request->Tamount,
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                 'season'  => $season,

            ];

            // Insert sale order record and get the inserted ID
           $saleOrderId = sale_details::insertGetId($saleOrderData);

                       $cnt = $request['cnt'];
$qtys = [];

                    for ($i = 1; $i <= $cnt; $i++) {

                        $service = $request['services_' . $i] ?? null;
                        $size = $request['size_' . $i] ?? null;
                        $stage = $request['stage_' . $i] ?? null;
                        $quantity = $request['quantity_' . $i] ?? 0;
                        $rate = $request['rate_' . $i] ?? 0;
                        $transper = $request['transper_' . $i] ?? 0;

                        $qty = $request['qty_' . $i] ?? null;
                        $gstper = $request['gstper_' . $i] ?? null;
                        $amount = $request['amount_' . $i] ?? null;
                        
                
                        if ($service && $size && $qty !== null) {
                            $serviceforapi[] = $service;
                            $sizeforapi[] = $size;
                            $qtys[] = $qty;
                        }


                        //distributeMangoes( $qty, $service,$size, $stage, $saleOrderId);


                        $saleOrderDetailData = [
                            'user_id'    => Auth::id(),
                            'pid'        => $saleOrderId, // Assuming you have the $id available
                            'services'   => $service,
                            'size'       => $size,
                            'stage'      => $stage,
                            'Quantity'   => $quantity,
                            'transper'   => $transper,
                            'qty'       => $qty,
                            'gstper'     => $gstper,
                            'amount'       => $amount,
                            'rate'       => $rate,
                            'update_id'  => Auth::id(),
                            'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        ];

                        // Insert sale order detail record
                     $saleOrderDetailDatainsert = sale_order::create($saleOrderDetailData);


                            if ($request->dispatch == 'yes') {
                                $count = DB::table('outward_details')->count();

                                
                                // Determine the next Bill_No based on existing records
                                $nextBillNo = $count > 0 ? $count + 1 : 1;

                                // Parse the bill date

                                    $amt = $qty - $quantity;
                 if ($quantity > 0) {
                                    $outwardData = [
                                        'user_id'       => Auth::id(),
                                        'Invoicenumber' => $nextBillNo,
                                        'billdate'      => $billDate,
                                        'customer_name' => $request->customer_name_sale,
                                        'order_no'      => $saleOrderId,
                                        'services'      => $service,
                                        'size'          => $size,
                                        'stage'         => $stage,
                                        'Quantity'      => $quantity,
                                        'qty'           => $qty,
                                        'rem_qty'       => $amt,
                                        'season'  => $season,
                                        'currdispatch_qty' =>$quantity,
                                        'update_id'     => Auth::id(),
                                        'created_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                                        'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                                    ];

                                    // Insert data into outward_details table
                                    outward_details::create($outwardData);

                                    // Increment the iterator
                                 }   //$i++;
                                }


                    }
                    
                    
                    if($request->amt_pay != 0){

                        $count = DB::table('purchase_payments')->count();

                        // Determine the next Bill_No based on existing records
                        $nextBillNo = $count > 0 ? $count + 1 : 1;
                        $salePaymnetData = [
                            'ReceiptNo'      =>   $nextBillNo,
                            'user_id'            => Auth::id(),
                            'updateduser'     => Auth::id(), // assuming it's the logged-in user
                            'PurchaseDate'    =>  $billDate,
                            'customer_name'  => $request->customer_name_sale,
                            'amt_pay'         => $request->amt_pay,
                            'totalvalue'     => $request->amt_pay,
                            'mode'  => $request->mode,
                            'cheque_no'       => $request->cheque_no,
                            'narration'    => $request->narration,
                             'sale_id'     => $saleOrderId,
                            'season'  => $season,
                            'date'           =>now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                            'cheque_amt'      => $request->cheque_amt,
                            'created_at'      => now()->format('Y-m-d'),
                            'updated_at'      => now()->format('Y-m-d'),
                        ];

                        $paymentinfo= Sale_payment::insertGetId($salePaymnetData);

                        $salePaymentDetailData = [
                            'pid'           => $paymentinfo, // assuming $salePaymentId is available
                            'Invoicenumber' => $saleOrderId,
                            'amount'         => $request->amt_pay,
                            'payamt'        => $request->Tamount,

                            'created_at'     => now()->format('Y-m-d'),
                            'updated_at'     => now()->format('Y-m-d'),
                        ];

                        // Insert sale order detail record
                        $salemsg =Sale_paymentDetails::create($salePaymentDetailData);
                        
                        
                    }
                 
                        
                            if($saleOrderDetailDatainsert){
                           
                            $amt = $request->amt_pay;
                            $totalamt=$request->Tamount;
                            // $billlink = "https://inventory.aamramm.com/sale-order/{$saleOrderId}/print";
                            // $paylink = "https://inventory.aamramm.com/razorpay/{$saleOrderId}";
                        //  $billlink = "https://inventory.aamramm.com/sale-order-print?id=".$saleOrderId;
                        //   $paylink = "https://inventory.aamramm.com/razorpay?id=".$saleOrderId;
                           
                           $billlink = "id=".$saleOrderId;   // ONLY ID
                            $paylink  ="id=". $saleOrderId; 
                     $results = [];
          

                    foreach ($serviceforapi as $key => $service) {
                        if (!empty($service) && isset($sizeforapi[$key])) { // Ensure service and size exist
                            $query = DB::table('products as p')
                                ->join('product_details as pd', 'p.id', '=', 'pd.parentID')
                                ->where('p.id', $service)
                                ->where('pd.id', $sizeforapi[$key]) // Correctly reference size
                                ->select('p.product_name', 'pd.product_size')
                                ->get();
                    
                            if (!$query->isEmpty()) {
                                foreach ($query as $item) {
                     $results[] = "{$item->product_name} - {$item->product_size} - Qty: {$qtys[$key]}";
                                }
                            }
                        }
                    }

                            // Convert results into a properly formatted string
                            $string = implode(", ", $results); // Properly joins with ", "
                            $existingCustomer = DB::table('customers')->where('id', $request->customer_name_sale)->first();
                            
                            if ($existingCustomer) {
                            $name = $existingCustomer->customer_name;
                            $mobnumber = "91" . $existingCustomer->wp_number;
                            } else {
                            return response()->json(['success' => false, 'message' => 'Customer not found']);
                            }
                            
                       $apiUrl = "http://wh.visionhlt.com/v23.0/991705040683041/messages";
                        $apiToken = "85ec58a7-e3b1-4553-a89f-faeba8309583";
                        
                        $data = [
                            "messaging_product" => "whatsapp",
                            "recipient_type" => "individual",
                            "to" => $mobnumber,
                            "type" => "template",
                            "template" => [
                                "name" => "updated_sales_inventory_sms",
                                "language" => ["code" => "en"],
                                "components" => [
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            ["type" => "text", "text" => $name],
                                            ["type" => "text", "text" => $string],
                                            ["type" => "text", "text" => $totalamt],
                                            ["type" => "text", "text" => $amt],
                                            ["type" => "text", "text" => $billlink],
                                            ["type" => "text", "text" => $paylink]
                                        ]
                                    ]
                                ]
                            ],
                            "biz_opaque_callback_data" => "sale_id_$saleOrderId"
                        ];
                        
                        // Send message using CURL
                        $ch = curl_init($apiUrl);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "Authorization: Bearer $apiToken",
                            "Content-Type: application/json"
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        
                        $response = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        
            if (curl_errno($ch)) {
                // Optional: log error, but DO NOT return
                // Log::error('WhatsApp CURL Error: ' . curl_error($ch));
            }

                curl_close($ch);              
                            }




                                    DB::commit();
                                    return redirect()->route('admin.sale_order.index')->with('success', __('Sale Order Created Successfully'));
                                } catch (Exception $e) {
                                    DB::rollback();
                                    throw $e;
                                }
                            }

    /**
     * Update a sale order record and its details.
     */
    public function update(array $request, $id)
    {


        DB::beginTransaction();
        try {
            $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
            // Update sale order details
            DB::table('sale_orderdetails')
                ->where('id', $id)
                ->update([
                    'user_id'        => Auth::id(),
                     'billdate'       => $billDate,
                     'PurchaseDate'  => $billDate,
                     'order_date'  => $billDate,
                     'Invoicenumber'  => $request['Invoicenumber'],
                    'batch_id'       => 1,
                    'totalproamt'    => $request['totalproamt'],
                    'customer_name'  => $request['customer_name_sale'],
                     'wholesaler'     =>$request['wholesaler'],
                      'order_address'  => $request['order_address'],
                    'gst'            => $request['gst'],
                    'dispatch'       => $request['dispatch'],
                    'discount_per'   => $request['discount_per'],
                    'discount_rupee' => $request['discount_rupee'],
                    'subtotal'       => $request['subtotal'],
                        'season'  => $season,
                    // 'trans_in_per'   => $request['trans_in_per'],
                    // 'trans_in_per_rs'=> $request['trans_in_per_rs'],
                    'other_charges'  => $request['other_charges'],
                    'CGST'           => $request['CGST'],
                    'SGST'           => $request['SGST'],
                    'IGST'           => $request['IGST'],
                    'mode'           => $request['mode'],
                    'amt_pay'        => $request['amt_pay'],
                    'narration'      => $request['narration'],
                    'Tamount'        => $request['Tamount'],
                    //'cheque_no'      => $request['cheque_no']?? 0,

                    'updated_at'     => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                ]);

            $cnt = $request['cnt'];

            $deletedRows = sale_order::where('pid', $id)->delete();
            $deletedRowss = outward_details::where('order_no', $id)->delete();
            // Delete existing records related to the given ID

     
            $paymentDetails = DB::table('purchase_payments')->where('sale_id', $id)->get();


            // Extract the Sale_payment IDs (pid) from the Sale_paymentDetails
            $paymentIds = $paymentDetails->pluck('id')->toArray();

            // Delete the related Sale_paymentDetails records
            DB::table('purchase_payments')->where('sale_id', $id)->delete();

            // Delete the related Sale_payment records using the extracted pids
            DB::table('purchase_payment_info')->whereIn('pid', $paymentIds)->delete();
            
           // $deletehistory=DB::table('batch_history')->where('orderid', $id)->delete();

           if($request['amt_pay'] != 0){


            $count = DB::table('purchase_payments')->count();
            $nextBillNo = $count > 0 ? $count + 1 : 1;

            // Insert new payment record
            $salePaymnetData = [
                'ReceiptNo'      => $nextBillNo,
                'user_id'        => Auth::id(),
                'updateduser'    => Auth::id(),
                'PurchaseDate'   => $billDate,
                'customer_name'  => $request['customer_name_sale'],
                'amt_pay'        => $request['amt_pay'],
                'totalvalue'     => $request['amt_pay'],
                'mode' => $request['mode'],
                'cheque_no'      => $request['cheque_no']?? 0,
                'narration'      => $request['narration'],
                'sale_id'        => $id,
                  'season'  => $season,
                'date'           => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'cheque_amt'     => $request['cheque_amt'] ?? 0,
                'created_at'     => now()->format('Y-m-d'),
                'updated_at'     => now()->format('Y-m-d'),
            ];

            $paymentinfo = Sale_payment::insertGetId($salePaymnetData);

            // Insert new sale payment detail record
            $salePaymentDetailData = [
                'pid'           => $paymentinfo,
                'Invoicenumber' => $id,
                'amount'        => $request['amt_pay'],
                'payamt'        => $request['Tamount'],
                'created_at'    => now()->format('Y-m-d'),
                'updated_at'    => now()->format('Y-m-d'),
            ];

            Sale_paymentDetails::create($salePaymentDetailData);

        }
        
        $qtys = [];

            for ($i = 1; $i <= $cnt; $i++) {
                $service = $request['services_' . $i] ?? null;
                $size = $request['size_' . $i] ?? null;
                $stage = $request['stage_' . $i] ?? null;
                $Quantity = $request['Quantity_' . $i] ?? 0;
                $rate = $request['rate_' . $i] ?? 0;
                $transper = $request['transper_' . $i] ?? 0;
                $qty = $request['qty_' . $i] ?? null;
                $gstper = $request['gstper_' . $i] ?? null;
                $amount = $request['amount_' . $i] ?? null;



    if ($service && $size && $qty !== null) {
                $serviceforapi[] = $service;
                $sizeforapi[] = $size;
                $qtys[] = $qty;
            }

                    sale_order::create([
                        'user_id'    => Auth::id(),
                        'pid'        => $id,
                        'services'   => $service,
                        'size'       => $size,
                        'stage'      => $stage,
                        'Quantity'   => $Quantity,
                        'transper'   => $transper,
                        'qty'        => $qty,
                        'gstper'     => $gstper,
                        'amount'     => $amount,
                        'rate'       => $rate,
                        'update_id'  => Auth::id(),
                        'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);


                    // Count existing outward details


                    $remainingQuantity = $qty - $Quantity;

  if ($request['dispatch'] == 'yes') {
                        $count = DB::table('outward_details')->count();
                        // Determine the next Bill_No based on existing records
                        $nextBillNo = $count > 0 ? $count + 1 : 1;

                        if ($Quantity > 0) {
                    outward_details::create([

                    'user_id'       => Auth::id(),
                    'Invoicenumber' => $nextBillNo,
                    'billdate'      => $billDate,
                    'customer_name' => $request['customer_name_sale'],
                    'order_no'      => $id,
                    'services'      => $service,
                    'size'          => $size,
                    'stage'         => $stage,
                    'Quantity'      => $Quantity,
                    'qty'           => $qty,
                      'season'  => $season,
                    'rem_qty'       => $remainingQuantity,
                   'currdispatch_qty' =>$Quantity,

                    'update_id'     => Auth::id(),
                    'created_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);
        }
    }

             
            }


                $amt = $request['amt_pay'];
                $totalamt=$request['Tamount'];
                        //   $billlink = "https://inventory.aamramm.com/sale-order-print?id=".$id;
                        //   $paylink = "https://inventory.aamramm.com/razorpay?id=".$id;
                        
                      $billlink = "id=".$id;   // ONLY ID
                            $paylink  = "id=".$id; 
        $results = [];


        foreach ($serviceforapi as $key => $service) {
            if (!empty($service) && isset($sizeforapi[$key])) { // Ensure service and size exist
                $query = DB::table('products as p')
                    ->join('product_details as pd', 'p.id', '=', 'pd.parentID')
                    ->where('p.id', $service)
                    ->where('pd.id', $sizeforapi[$key]) // Correctly reference size
                    ->select('p.product_name', 'pd.product_size')
                    ->get();

                if (!$query->isEmpty()) {
                                foreach ($query as $item) {
                    $results[] = "{$item->product_name} - {$item->product_size} - Qty: {$qtys[$key]}";
                                }
                            }
            }
        }

                // Convert results into a properly formatted string
                $string = implode(", ", $results); // Properly joins with ", "
                $existingCustomer = DB::table('customers')->where('id', $request['customer_name_sale'])->first();
                if ($existingCustomer) {
                $name = $existingCustomer->customer_name;
                $mobnumber = "91" . $existingCustomer->wp_number;
                } else {
                return response()->json(['success' => false, 'message' => 'Customer not found']);
                }
   $apiUrl = "http://wh.visionhlt.com/v23.0/991705040683041/messages";
                        $apiToken = "85ec58a7-e3b1-4553-a89f-faeba8309583";
                        
                        $data = [
                            "messaging_product" => "whatsapp",
                            "recipient_type" => "individual",
                            "to" => $mobnumber,
                            "type" => "template",
                            "template" => [
                                "name" => "updated_sales_inventory_sms",
                                "language" => ["code" => "en"],
                                "components" => [
                                    [
                                        "type" => "body",
                                        "parameters" => [
                                            ["type" => "text", "text" => $name],
                                            ["type" => "text", "text" => $string],
                                            ["type" => "text", "text" => $totalamt],
                                            ["type" => "text", "text" => $amt],
                                            ["type" => "text", "text" => $billlink],
                                            ["type" => "text", "text" => $paylink]
                                        ]
                                    ]
                                ]
                            ],
                            "biz_opaque_callback_data" => "sale_id_$id"
                        ];
                        
                        // Send message using CURL
                        $ch = curl_init($apiUrl);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "Authorization: Bearer $apiToken",
                            "Content-Type: application/json"
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        
                        $response = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                
                    if (curl_errno($ch)) {
                        // Optional: log error, but DO NOT return
                        // Log::error('WhatsApp CURL Error: ' . curl_error($ch));
                    }

                curl_close($ch);  
                 echo $response;
            DB::commit();
            return redirect()->route('admin.sale_order.index')->with('success', __('Sale Order Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    /**
     * Delete a sale order record and its associated details.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Find the sale order by ID
            $saleOrder = sale_details::findOrFail($id);

            // Delete associated sale order details
            sale_order::where('pid', $saleOrder->id)->delete();

         // Get all Sale_paymentDetails records associated with the given Invoice number
$paymentDetails = DB::table('purchase_payment_info')->where('Invoicenumber', $saleOrder->id)->get();


// Extract the Sale_payment IDs (pid) from the Sale_paymentDetails
$paymentIds = $paymentDetails->pluck('pid')->toArray();

// Delete the related Sale_paymentDetails records
DB::table('purchase_payment_info')->where('Invoicenumber', $saleOrder->id)->delete();

// Delete the related Sale_payment records using the extracted pids
DB::table('purchase_payments')->whereIn('id', $paymentIds)->delete();

outward_details::where('order_no', $saleOrder->id)->delete();

DB::table('batch_history')->where('orderid', $saleOrder->id)->delete();

// DB::table('razorpay_history')->where('order_id', $saleOrder->id)->delete();

            // Delete the sale order record
            $saleOrder->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Sale Order Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
