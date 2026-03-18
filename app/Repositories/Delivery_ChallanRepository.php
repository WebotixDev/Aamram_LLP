<?php

namespace App\Repositories;

use App\Models\Delivery_Challan;
use App\Models\Delivery_ChallanDetails;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Delivery_ChallanRepository
{
    /**
     * Store a new delivery challan and its details.
     */
    public function store(Request $request)
{
        $season = session('selected_season');
    // $season = session('selected_season', date('Y'));

    DB::beginTransaction();
    try {
        // Insert into delivery_challan table
        $challan =[
            'user_id' => Auth::id(),
            'Invoicenumber' => $request->Invoicenumber,
            'billdate' => Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d'),
            'transporter' => $request->transporter,
              'season'  => $season,
            'created_at' => now(),
        ];

        $challanId = Delivery_Challan::insertGetId($challan);


        // Retrieve selected outward_details records and insert into delivery_challan_details
        $selectedIds = $request->input('selected_idstot', []);

        // Ensure selectedIds is always an array
        if (!is_array($selectedIds)) {
            $selectedIds = explode(',', $selectedIds); // If coming as a comma-separated string
        }

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'No outward details selected.');
        }

        $outwardDetails = DB::table('outward_details')->whereIn('id', $selectedIds)->get();

        foreach ($outwardDetails as $detail) {
            Delivery_ChallanDetails::create([
                'otid' =>$detail->id,
                'pid' => $challanId,
                'user_id' => Auth::id(),
                'billdate' => $detail->billdate,
                'customer_name' => $detail->customer_name,
                'order_no' => $detail->order_no,
                'services' => $detail->services,
                'size' => $detail->size,
                'stage' => $detail->stage,
                'qty' => $detail->qty,
                'currdispatch_qty' => $detail->currdispatch_qty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update outward_details complete_flag
            DB::table('outward_details')
                ->where('id', $detail->id)
                ->update(['flag' => '1']);
        }

        DB::commit();
        return redirect()->route('admin.Delivery_Challan.index')->with('success', __('Delivery Challan Created Successfully'));
    } catch (Exception $e) {
        DB::rollback();
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}


    /**
     * Update an existing delivery challan.
     */
     public function update(Request $request, $id)
    {

       // dd($request);

    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
        DB::beginTransaction();
        try {
            // Update delivery_challan table
            $challan = Delivery_Challan::findOrFail($id);
            $challan->update([
                'billdate' => Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d'),
                'transporter' => $request->transporter,
                'updated_at' => now(),
                   'season'  => $season,
            ]);

            // Get existing selected IDs related to this challan and reset their flag to 0
            $existingDetails = Delivery_ChallanDetails::where('pid', $id)->get();
            $existingIds = $existingDetails->pluck('otid')->toArray();

            DB::table('outward_details')->whereIn('id', $existingIds)->update(['flag' => '0']);

            // Delete existing details
            Delivery_ChallanDetails::where('pid', $id)->delete();

            // Ensure $selectedIds is an array
            $selectedIds = $request->input('selected_idstot', []);
            if (!is_array($selectedIds)) {
                $selectedIds = explode(',', $selectedIds);
            }

            $outwardDetails = DB::table('outward_details')->whereIn('id', $selectedIds)->get();

            foreach ($outwardDetails as $detail) {
                Delivery_ChallanDetails::create([
                    'otid' => $detail->id,
                    'pid' => $challan->id,
                    'user_id' => Auth::id(),
                    'billdate' => $detail->billdate,
                    'customer_name' => $detail->customer_name,
                    'order_no' => $detail->order_no,
                    'services' => $detail->services,
                    'size' => $detail->size,
                    'stage' => $detail->stage,
                    'qty' => $detail->qty,
                    'currdispatch_qty' => $detail->currdispatch_qty,
                    'updated_at' => now(),
                ]);
            }

            // Update flag to 1 for newly selected IDs
            DB::table('outward_details')->whereIn('id', $selectedIds)->update(['flag' => '1']);

            DB::commit();
            return redirect()->route('admin.Delivery_Challan.index')->with('success', __('Delivery Challan Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a delivery challan and its details.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Retrieve the challan details related to the given challan ID
            $challanDetails = Delivery_ChallanDetails::where('pid', $id)->get();

            // Delete the related delivery challan details
            Delivery_ChallanDetails::where('pid', $id)->delete();

            // Delete the delivery challan
            Delivery_Challan::findOrFail($id)->delete();

            // Loop through each challan detail to update the outward_details flag
            foreach ($challanDetails as $detail) {
                DB::table('outward_details')
                    ->where('id', $detail->otid)
                    ->update(['flag' => '0']);
            }

            DB::commit();
            return redirect()->back()->with('success', __('Delivery Challan Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

}
