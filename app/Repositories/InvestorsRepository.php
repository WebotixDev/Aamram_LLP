<?php

namespace App\Repositories;

use App\Models\Investor;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class InvestorsRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Investor::class;
    }

    /**
     * Store a new sale payment record along with its details in the database.
     */
    public function store(Request $request)
    {
        $PurchaseDate = Carbon::createFromFormat('d-m-Y', $request->PurchaseDate)->format('Y-m-d');
        $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');


        DB::beginTransaction();
        try {
            // Prepare the SalePayment data
            $investPaymentData = [
                'user_id'            => Auth::id(),
                'updateduser'     => Auth::id(), // assuming it's the logged-in user
                'locationID'      => $request->locationID,
                'PurchaseDate'    => $PurchaseDate,
                'investor_name'  => $request->investor_name,
                'amt_pay'         => $request->amt_pay,
                'mode'  => $request->mode,
                'type'         => $request->type,
                'cheque_no'       => $request->cheque_no,
                'narration'    => $request->narration,
                'date'           =>$date,
                'ReceiptNo'       => $request->ReceiptNo,
                'created_at'      => now()->format('Y-m-d'),
                'updated_at'      => now()->format('Y-m-d'),
            ];

            // Insert sale payment record and get the inserted ID
            Investor::create($investPaymentData);




            DB::commit();
            return redirect()->route('admin.investors.index')->with('success', __('Created Successfully'));
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
         $PurchaseDate = Carbon::createFromFormat('d-m-Y', $request['PurchaseDate'])->format('Y-m-d');
         $date = Carbon::createFromFormat('d-m-Y', $request['date'])->format('Y-m-d');

         DB::beginTransaction();
         try {
             $investPaymentDatas = [
                 'user_id'       => Auth::id(),
                 'updateduser'   => Auth::id(),
                 'PurchaseDate'  => $PurchaseDate,
                 'investor_name' => $request['investor_name'],
                 'amt_pay'       => $request['amt_pay'],
                 'mode'          => $request['mode'],
                 'type'          => $request['type'],
                 'cheque_no'     => $request['cheque_no'],
                 'narration'     => $request['narration'],
                 'date'          => $date,
                 'ReceiptNo'     => $request['ReceiptNo'],
                 'updated_at'    => now()->format('Y-m-d'),
             ];

             // ✅ fetch instance, then update
             $investor = Investor::findOrFail($id);
             $investor->update($investPaymentDatas);

             DB::commit();
             return redirect()->route('admin.investors.index')->with('success', __('Updated Successfully'));

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
            $saleOrder = Investor::findOrFail($id);

            $saleOrder->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
