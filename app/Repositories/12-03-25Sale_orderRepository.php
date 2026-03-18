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


        DB::beginTransaction();
        try {
            $saleOrderData = [
                'user_id'        => Auth::id(),
                'billdate'       => $billDate,
                'PurchaseDate'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'order_date'  => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                'Invoicenumber'  => $request->Invoicenumber,
                'batch_id'       => 1,
                'totalproamt'=>  $request->totalproamt,
                'customer_name'  => $request->customer_name_sale,
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
            ];

            // Insert sale order record and get the inserted ID
           $saleOrderId = sale_details::insertGetId($saleOrderData);

 /////// /////////////////////////////////////Distribute mangoes function//////////////////////////////////////

            function distributeMangoes($orderQuantity, $productId,$size, $stage, $saleOrderId) {
                // Fetch batch details for the specific product
                $batchDetails = DB::table('purchase_product as pp')
                    ->join('purchase_details as pd', 'pd.id', '=', 'pp.pid')
                    ->where('pp.services', $productId)
                    ->where('pp.size', $size)
                    ->where('pp.stage', $stage)
                    ->where('pp.complete_flag', 0)
                    ->select('pd.batch_id', 'pp.quantity', 'pp.services', 'pd.id as purchase_detail_id')
                    ->orderBy('pd.created_at', 'asc') // Ensures batches are retrieved in ascending order
                    ->get();





                $remainingQuantity = $orderQuantity;

                foreach ($batchDetails as $batch) {
                    if ($remainingQuantity <= 0) {
                        break; // Stop if all required quantity is fulfilled
                    }

                    // Calculate the quantity to deduct
                    $deductQuantity = min($remainingQuantity, $batch->quantity);
                    $remainingQuantity -= $deductQuantity;

                    $newQuantity = $batch->quantity - $deductQuantity;

                    // Insert into batch history instead of updating purchase_product
                    $ocupprdbatch=DB::table('batch_history')->insert([
                        'user_id'        => Auth::id(),
                        'batch_id'       => $batch->batch_id,
                        'orderid'        => $saleOrderId,
                        'deducted_qty'   => $deductQuantity,
                        'productid'      => $productId,
                        'sizeid'         => $size,
                        'stageid'        => $stage,
                        'rem_qty'        => $newQuantity,
                        'created_at'     => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);
                }


                // Handle case where not enough quantity is available
                if ($remainingQuantity > 0) {
                    throw new Exception("Insufficient quantity available across batches.");
                }

                // Check and update the complete_flag for affected batches based on batch_history
                $affectedBatchIds = $batchDetails->pluck('batch_id')->unique();

                foreach ($affectedBatchIds as $batchId) {
                    // Check the total remaining quantity in the batch using batch_history
                    $totalRemainingQuantity = DB::table('batch_history')
                        ->where('batch_id', $batchId)
                        ->where('productid', $productId)
                        ->where('sizeid', $size)
                        ->where('stageid', $stage)
                        ->sum('rem_qty');



                    // If no products remain in the batch, mark it as complete
                    if ($totalRemainingQuantity == 0) {
                        DB::table('purchase_product')
                            ->where('batch_id', $batchId)
                            ->where('services', $productId)
                            ->where('size', $size)
                            ->where('stage', $stage)
                            ->update([
                                'complete_flag' => 1,
                                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
                       $cnt = $request['cnt'];

                    for ($i = 1; $i <= $cnt; $i++) {

                        $service = $request['services' . $i] ?? null;
                        $size = $request['size' . $i] ?? null;
                        $stage = $request['stage' . $i] ?? null;
                        $quantity = $request['quantity' . $i] ?? 0;
                        $rate = $request['rate' . $i] ?? 0;
                        $transper = $request['transper' . $i] ?? 0;

                        $qty = $request['qty' . $i] ?? null;
                        $gstper = $request['gstper' . $i] ?? null;
                        $amount = $request['amount' . $i] ?? null;


                        distributeMangoes( $qty, $service,$size, $stage, $saleOrderId);


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
                     sale_order::create($saleOrderDetailData);


                            if ($request->dispatch == 'yes') {
                                $count = DB::table('outward_details')->count();

                                // Determine the next Bill_No based on existing records
                                $nextBillNo = $count > 0 ? $count + 1 : 1;

                                // Parse the bill date
                                $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');

                                // Assuming `$i` refers to multiple dynamic inputs
                                //$j = 1; // Replace with the actual loop or iteration logic, e.g., a `foreach` loop for services

                                // while (isset($request['services1'])) {
                                //     $service = $request['services' . $i] ?? null;
                                //     $size = $request['size' . $i] ?? null;
                                //     $stage = $request['stage' . $i] ?? null;
                                //     $quantity = $request['quantity' . $i] ?? 0;
                                //     $qty = $request['qty' . $i] ?? null;

                                    // Calculate remaining quantity

                                    $amt = $qty - $quantity;

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
                                        // 'currdispatch_qty' =>$amt,
                                        'update_id'     => Auth::id(),
                                        'created_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                                        'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                                    ];

                                    // Insert data into outward_details table
                                    outward_details::create($outwardData);

                                    // Increment the iterator
                                    //$i++;
                                }


                    }

                        $count = DB::table('purchase_payments')->count();

                        // Determine the next Bill_No based on existing records
                        $nextBillNo = $count > 0 ? $count + 1 : 1;
                        $salePaymnetData = [
                            'ReceiptNo'      =>   $nextBillNo,
                            'user_id'            => Auth::id(),
                            'updateduser'     => Auth::id(), // assuming it's the logged-in user
                            'PurchaseDate'    =>  now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                            'customer_name'  => $request->customer_name_sale,
                            'amt_pay'         => $request->amt_pay,
                            'totalvalue'     => $request->amt_pay,
                            'mode'  => $request->mode,
                            'cheque_no'       => $request->cheque_no,
                            'narration'    => $request->narration,
                             'sale_id'     => $saleOrderId,
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
                        Sale_paymentDetails::create($salePaymentDetailData);
                        
                        //   $curl = curl_init();
                        
                        // curl_setopt_array($curl, array(
                        //   CURLOPT_URL => 'https://backend.aisensy.com/campaign/t1/api/v2',
                        //   CURLOPT_RETURNTRANSFER => true,
                        //   CURLOPT_ENCODING => '',
                        //   CURLOPT_MAXREDIRS => 10,
                        //   CURLOPT_TIMEOUT => 0,
                        //   CURLOPT_FOLLOWLOCATION => true,
                        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        //   CURLOPT_CUSTOMREQUEST => 'POST',
                        //   CURLOPT_POSTFIELDS =>'{
                        //   "apiKey": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY3YmVhYTYzOGUwZjVmMGMxYTliOTI0MiIsIm5hbWUiOiJBYW1yYW0gTWFuZ28gMiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2N2E5ZGU5YmVmMGQ4ZjBjMGRkOGM5NjciLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzQwNTQ4NzA3fQ.YyT1y2n5MMvcA4yVZZ6AoNLmAliEwMcRcUCn-3Ym8ak",
                        //   "campaignName": "saleorderaamram",
                        //   "destination": "918788201316",
                        //   "userName": "Aamram Mango 2",
                        //   "templateParams": [],
                        //   "source": "new-landing-page form",
                        //   "media": {},
                        //   "buttons": [],
                        //   "carouselCards": [],
                        //   "location": {},
                        //   "attributes": {},
                        //   "paramsFallbackValue": {}
                        // }',
                        //   CURLOPT_HTTPHEADER => array(
                        //     'Content-Type: application/json'
                        //   ),
                        // ));
                        
                        // $response = curl_exec($curl);
                        
                        // curl_close($curl);
                        // echo $response;
                        
                        
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

            // Update sale order details
            DB::table('sale_orderdetails')
                ->where('id', $id)
                ->update([
                    'user_id'        => Auth::id(),
                    'billdate'       => $billDate,
                    'PurchaseDate'   => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                    'order_date'     => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                    'Invoicenumber'  => $request['Invoicenumber'],
                    'batch_id'       => 1,
                    'totalproamt'    => $request['totalproamt'],
                    'customer_name'  => $request['customer_name_sale'],
                    'gst'            => $request['gst'],
                    'dispatch'       => $request['dispatch'],
                    'discount_per'   => $request['discount_per'],
                    'discount_rupee' => $request['discount_rupee'],
                    'subtotal'       => $request['subtotal'],
                    // 'trans_cost'     => $request['trans_cost'],
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

            for ($i = 1; $i <= $cnt; $i++) {
                $service = $request['services' . $i] ?? null;
                $size = $request['size' . $i] ?? null;
                $stage = $request['stage' . $i] ?? null;
                $Quantity = $request['Quantity' . $i] ?? 0;
                $rate = $request['rate' . $i] ?? 0;
                $transper = $request['transper' . $i] ?? 0;

                $qty = $request['qty' . $i] ?? null;
                $gstper = $request['gstper' . $i] ?? null;
                $amount = $request['amount' . $i] ?? null;

                // Update or create sale order details
                $existingOrderDetail = sale_order::where('pid', $id)
                    ->where('services', $service)
                    ->where('size', $size)
                    ->where('stage', $stage)
                    ->first();


                if ($existingOrderDetail) {
                    $existingOrderDetail->update([
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
                        'updated_at'=> now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);


                } else {
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
                }

                  // Update outward details
                  $existingOutwardDetail = outward_details::where('order_no', $id)
                  ->where('services', $service)
                  ->where('size', $size)
                  ->where('stage', $stage)
                  ->first();

              $remainingQuantity = $qty - $Quantity;

              if ($existingOutwardDetail) {
                  $existingOutwardDetail->update([
                      'rem_qty'    => $remainingQuantity,
                      'qty'        => $qty,
                      'Quantity'   => $Quantity,
                      'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                  ]);
              } else {
                  outward_details::create([
                      'user_id'       => Auth::id(),
                      'Invoicenumber' => $id,
                      'billdate'      => $billDate,
                      'customer_name' => $request['customer_name_sale'],
                      'order_no'      => $id,
                      'services'      => $service,
                      'size'          => $size,
                      'stage'         => $stage,
                      'Quantity'      => $Quantity,
                      'qty'           => $qty,
                      'rem_qty'       => $remainingQuantity,
                      'update_id'     => Auth::id(),
                      'created_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                      'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                  ]);
              }


                // Update purchase products complete_flag
                DB::table('purchase_product')
                    ->where('size', $size)
                    ->where('stage', $stage)
                    ->where('services', $service)
                    ->update([
                        'complete_flag' => 0,
                        'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);

                // Delete existing batch history for the combination
                DB::table('batch_history')
                    ->where('orderid', $id)
                    ->where('productid', $service)
                    ->where('sizeid', $size)
                    ->where('stageid', $stage)
                    ->delete();

                // Insert new batch history
                $batchDetails = DB::table('purchase_product as pp')
                    ->join('purchase_details as pd', 'pd.id', '=', 'pp.pid')
                    ->where('pp.services', $service)
                    ->where('pp.size', $size)
                    ->where('pp.stage', $stage)
                    ->where('pp.complete_flag', 0)
                    ->select('pd.batch_id', 'pp.quantity', 'pp.services', 'pd.id as purchase_detail_id')
                    ->orderBy('pd.created_at', 'asc')
                    ->get();

                $remainingQuantityForBatch = $qty;

                foreach ($batchDetails as $batch) {
                    if ($remainingQuantityForBatch <= 0) {
                        break;
                    }

                    $deductQuantity = min($remainingQuantityForBatch, $batch->quantity);
                    $remainingQuantityForBatch -= $deductQuantity;

                    $newQuantity = $batch->quantity - $deductQuantity;

                    DB::table('batch_history')->insert([
                        'user_id'      => Auth::id(),
                        'batch_id'     => $batch->batch_id,
                        'orderid'      => $id,
                        'deducted_qty' => $deductQuantity,
                        'productid'    => $service,
                        'sizeid'       => $size,
                        'stageid'      => $stage,
                        'rem_qty'      => $newQuantity,
                        'created_at'   => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ]);
                }

                // Update complete_flag based on batch history
                foreach ($batchDetails as $batch) {
                    $totalRemainingQuantity = DB::table('batch_history')
                        ->where('batch_id', $batch->batch_id)
                        ->where('productid', $service)
                        ->where('sizeid', $size)
                        ->where('stageid', $stage)
                        ->sum('rem_qty');

                    if ($totalRemainingQuantity == 0) {
                        DB::table('purchase_product')
                            ->where('batch_id', $batch->batch_id)
                            ->where('services', $service)
                            ->where('size', $size)
                            ->where('stage', $stage)
                            ->update([
                                'complete_flag' => 1,
                                'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            ]);
                    }
                }



              

                    // Delete existing records related to the given ID
                    Sale_payment::where('sale_id', $id)->delete();
                    Sale_paymentDetails::where('Invoicenumber', $id)->delete();

                    // Determine the next Bill_No based on existing records
                    $count = DB::table('purchase_payments')->count();
                    $nextBillNo = $count > 0 ? $count + 1 : 1;

                    // Insert new payment record
                    $salePaymnetData = [
                        'ReceiptNo'      => $nextBillNo,
                        'user_id'        => Auth::id(),
                        'updateduser'    => Auth::id(),
                        'PurchaseDate'   => now()->setTimezone('Asia/Kolkata')->format('Y-m-d'),
                        'customer_name'  => $request['customer_name_sale'],
                        'amt_pay'        => $request['amt_pay'],
                        'totalvalue'     => $request['amt_pay'],
                        'mode' => $request['mode'],
                        'cheque_no'      => $request['cheque_no']?? 0,
                        'narration'      => $request['narration'],
                        'sale_id'        => $id,
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
$paymentDetails = Sale_paymentDetails::where('Invoicenumber', $saleOrder->id)->get();


// Extract the Sale_payment IDs (pid) from the Sale_paymentDetails
$paymentIds = $paymentDetails->pluck('pid')->toArray();

// Delete the related Sale_paymentDetails records
Sale_paymentDetails::where('Invoicenumber', $saleOrder->id)->delete();

// Delete the related Sale_payment records using the extracted pids
Sale_payment::whereIn('id', $paymentIds)->delete();

            
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
