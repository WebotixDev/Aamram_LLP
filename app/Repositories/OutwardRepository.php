<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\outward;
use App\Models\outward_details;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class OutwardRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return outward_details::class;
    }

    /**
     * Store a new outward entry along with its details in the database.
     */
    public function store(Request $request)
    {
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
        DB::beginTransaction();
        try {
        $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');
            $cnt = count($request->services); // Number of outward details

            for ($i = 0; $i < $cnt; $i++) {


                // Check if each key exists in the request and has a valid value
                $service = isset($request->services[$i]) ? $request->services[$i] : null;
                $size = isset($request->size[$i]) ? $request->size[$i] : null;
                $stage = isset($request->stage[$i]) ? $request->stage[$i] : null;
                $quantity = isset($request->Quantity[$i]) ? $request->Quantity[$i] : null;
                $qty = isset($request->qty[$i]) ? $request->qty[$i] : null;
                $rem_qty = isset($request->rem_qty[$i]) ? $request->rem_qty[$i] : null;
                $dispatch = isset($request->dispatch[$i]) ? $request->dispatch[$i] : null;
                $currdispatch_qty = isset($request->currdispatch_qty[$i]) ? $request->currdispatch_qty[$i] : null;

       $rem_qunatity = $rem_qty - $currdispatch_qty;

       if($currdispatch_qty != 0){


                    $outwardData = [
                        'user_id'        => Auth::id(),
                        'billdate'       => $billDate,
                        'Invoicenumber'  => $request->Invoicenumber,
                        'batch_id'       => $request->batch_id,
                        'customer_name'  => $request->customer_name,
                        'order_no'       => $request->order_no,
                        'services'       => $service,
                        'size'           => $size,
                        'stage'          => $stage,
                        'Quantity'       => $quantity,
                        'qty'            => $qty,
                        'currdispatch_qty' =>$currdispatch_qty,
                        'dispatch'     => $dispatch,
                        'rem_qty'       => $rem_qunatity,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                        'update_id'  => Auth::id(),
                        'flag'=>1,
                         'season'  => $season,

                    ];

                    // Insert outward record
                    outward_details::create($outwardData);
       }
            }

            DB::commit();
            return redirect()->route('admin.outward.index')->with('success', __('Outward Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



    /**
     * Update an outward entry and its details.
     */
      public function update(array $request, $id)
      {


        DB::beginTransaction();
        try {

         DB::table('outward_details')
         ->where('id',  $id)
         ->update([
             'currdispatch_qty' => $request['currdispatch_qty'],
             'update_id'  => Auth::id(),
             'updated_at' => now(),
         ]);


            DB::commit();
            return redirect()->route('admin.outward.index')->with('success', __('Outward Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an outward entry and its associated details.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $outwardDetails = outward_details::findOrFail($id);

            $outwardDetails->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Outward Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
