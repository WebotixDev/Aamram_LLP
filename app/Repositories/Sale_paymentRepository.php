<?php

namespace App\Repositories;

use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class Sale_paymentRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Sale_payment::class;
    }

    /**
     * Store a new sale payment record along with its details in the database.
     */
    public function store(Request $request)
    {
        $PurchaseDate = Carbon::createFromFormat('d-m-Y', $request->PurchaseDate)->format('Y-m-d');
        $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));

        DB::beginTransaction();
        try {
            // Prepare the SalePayment data
            $salePaymentData = [
                'user_id'            => Auth::id(),
                'updateduser'     => Auth::id(), // assuming it's the logged-in user
                'locationID'      => $request->locationID,
                'PurchaseDate'    => $PurchaseDate,
                'customer_name'  => $request->customer_name,
                'amt_pay'         => $request->amt_pay,
                'totalvalue'     => $request->totalvalue,
                'mode'  => $request->mode,
                'cheque_no'       => $request->cheque_no,
                'narration'    => $request->narration,
                'date'           =>$date,
                'complete_flag'          => 'cash',
                'season'  => $season,
                'ReceiptNo'       => $request->ReceiptNo,
                'created_at'      => now()->format('Y-m-d'),
                'updated_at'      => now()->format('Y-m-d'),
            ];

            // Insert sale payment record and get the inserted ID
            $salePaymentId = Sale_payment::insertGetId($salePaymentData);

            // Now handle the payment details if provided
            // $cnt = $request->cnt;
            // dd($cnt);
           // die;  // Number of payment details

// Count the number of entries
                $cnt = count($request->purchaseid); // Ensure this is the correct count from your request

                // Loop through the form entries to insert into the database
                for ($i = 0; $i < $cnt; $i++) {

                    // Get values from the request data (handle undefined and null gracefully)
                    $purchaseid = isset($request->purchaseid[$i]) && $request->purchaseid[$i] !== 'undefined' ? $request->purchaseid[$i] : null;
                    $bankAmount = isset($request->bank[$i]) ? $request->bank[$i] : 0;
                    $invoicenumber = isset($request->Invoicenumber[$i]) ? $request->Invoicenumber[$i] : 0;
                    $payamt = isset($request->payamt[$i]) ? $request->payamt[$i] : 0;



                    // Skip the row if bankAmount is 0
                    if ($bankAmount == 0) {
                        continue; // Skip this iteration and move to the next one
                    }

                    // Prepare the payment detail data
                    $paymentDetailData = [
                        'pid'           => $salePaymentId, // assuming $salePaymentId is available
                        'Invoicenumber' => $invoicenumber,
                        'purchaseid'    => $purchaseid,
                        'amount'         => $bankAmount,
                        'payamt'        => $payamt,

                        'created_at'     => now()->format('Y-m-d'),
                        'updated_at'     => now()->format('Y-m-d'),
                    ];

                    // Insert the payment detail record into the database
                    Sale_paymentDetails::create($paymentDetailData);
                }


            DB::commit();
            return redirect()->route('admin.sale_payment.index')->with('success', __('Sale Payment Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing sale payment record and its details.
     */

     public function update(array $request, $id)
     {
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));

         $PurchaseDate = Carbon::createFromFormat('d-m-Y', $request['PurchaseDate'])->format('Y-m-d');
         $date = Carbon::createFromFormat('d-m-Y', $request['date'])->format('Y-m-d');

         DB::beginTransaction();
         try {
             // Prepare the SalePayment data for update
             $salePaymentData = [
                 'user_id'          => Auth::id(),
                 'updateduser'      => Auth::id(),
                 'PurchaseDate'     => $PurchaseDate,
                 'customer_name'    => $request['customer_name'],
                 'amt_pay'          => $request['amt_pay'],
                 'totalvalue'       => $request['totalvalue'],
                 'mode'   => $request['mode'],
                 'cheque_no'        => $request['cheque_no'],
                 'narration'        => $request['narration'],
                 'date'             => $date,
                'season'  => $season,
                 'ReceiptNo'        => $request['ReceiptNo'],
                 'updated_at'       => now()->format('Y-m-d'),
             ];


             // Update the Sale Payment record
             Sale_payment::where('id', $id)->update($salePaymentData);

             // Delete existing payment details related to this Sale Payment
             Sale_paymentDetails::where('pid', $id)->delete();

             // Handle the updated payment details
             $cnt = (int)$request['cnt']; // Convert cnt to an integer
             for ($i = 1; $i <= $cnt; $i++) { // Start from 1 to match the suffix in keys

                 $purchaseid = isset($request["purchaseid_$i"]) && $request["purchaseid_$i"] !== 'undefined' ? $request["purchaseid_$i"] : null;
                 $bankAmount = isset($request["bank_$i"]) ? $request["bank_$i"] : 0;
                 $invoicenumber = isset($request["Invoicenumber_$i"]) ? $request["Invoicenumber_$i"] : 0;
                 $payamt = isset($request["payamt_$i"]) ? $request["payamt_$i"] : 0;

                 // Skip the row if bankAmount is 0
                 if ($bankAmount == 0) {
                     continue;
                 }

                 // Prepare the payment detail data
                 $paymentDetailData = [
                     'pid'            => $id, // Use the existing Sale Payment ID
                     'Invoicenumber'  => $invoicenumber,
                     'purchaseid'     => $purchaseid,
                     'amount'         => $bankAmount,
                     'payamt'         => $payamt,
                     'created_at'     => now()->format('Y-m-d'),
                     'updated_at'     => now()->format('Y-m-d'),
                 ];

                 // Insert the payment detail record into the database
                 Sale_paymentDetails::create($paymentDetailData);
             }


             DB::commit();
             return redirect()->route('admin.sale_payment.index')->with('success', __('Sale Payment Updated Successfully'));
         } catch (Exception $e) {
             DB::rollback();
             throw $e;
         }
     }





    /**
     * Delete a sale payment record and its associated payment details.
     */
    public function destroy($id)
    {


        DB::beginTransaction();
        try {
            $saleOrder = Sale_payment::findOrFail($id);


            // Delete associated payment details
            Sale_paymentDetails::where('pid', $saleOrder->id)->delete();

            // Delete the Sale Payment record
            $saleOrder->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Sale Payment Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
