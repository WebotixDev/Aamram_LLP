<?php

namespace App\Repositories;

use App\Models\Ripening_Chamber;
use App\Models\Ripening_Chamber_details;
use App\Models\Product_details;
use App\Models\Location;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class RipeningChamberRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Ripening_Chamber::class;
    }

    /**
     * Store a new inward record along with its details in the database.
     */
    public function store(Request $request)
    {

    $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');

    $season = session('selected_season');

     $location = $request->receive_location_id;



    $locationname = Location::find($location);
    $loname = $locationname ? $locationname->location : null;


        $invoice = \App\Helpers\Helpers::getNextInvoiceForWarehouseInward($request->receive_location_id);

        $invoiceNumber = $invoice['formatted'];

        DB::beginTransaction();
        try {
            $inward = [
                'user_id'    => Auth::id(),
                'billdate' => $billDate,
                'warehouse_inward_No'=> $request->warehouse_inward_No,
                'receive_location_id'=> $request->receive_location_id,
                'receive_location_name'    =>$loname,
                'Invoicenumber' => $invoiceNumber,
                'invoice_no'    => $invoice['number'],
                 'update_id'   => Auth::id(),
                 'season'  => $season,
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),

            ];

                $inwardId = Ripening_Chamber::insertGetId($inward);

               $services = $request->services;
                $sizes = $request->size;
                $stages = $request->stage;
                $quantities = $request->Quantity;
                $batches = $request->batch_number;
                $chamber_qty = $request->chamber_qty;

                for ($i = 0; $i < count($services); $i++) {

    $received = $chamber_qty[$i] ?? 0;

    if ($received == 0) {
        continue;
    }
                    $sizeData = Product_details::find($sizes[$i]);
                    $size_name = $sizeData ? $sizeData->product_size : null;

                    $inwardDetailData = [
                        'user_id'    => Auth::id(),
                        'pid'        => $inwardId,
                        'services'   => $services[$i],
                        'size'       => $sizes[$i],
                        'size_name'  => $size_name,
                        'stage'      => $stages[$i],
                        'Quantity'   => $quantities[$i],
                        'batch_number' => $batches[$i],
                        'chamber_qty' => $chamber_qty[$i] ?? 0,
                        'update_id'  => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    Ripening_Chamber_details::create($inwardDetailData);
                }


                            DB::commit();
                            return redirect()->route('admin.ripening_chamber.index')->with('success', __('Ripening Chamber Created Successfully'));
                        } catch (Exception $e) {
                            DB::rollback();
                            throw $e;
                        }
                    }

    /**
     * Update an inward record and its details.
     */
public function update(array $request, $id)
{
    DB::beginTransaction();

    try {
        $season = session('selected_season');

        $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');

        $location = $request['receive_location_id'];

        // Check if location changed
        $originalLocation = $request['original_location_id'] ?? null;

        if ($location != $originalLocation) {
            $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmDC($location);
            $invoiceNumber = $invoice['formatted'];
            $invoice_no = $invoice['number'];
        } else {
            // Keep original invoice
            $invoiceNumber = $request['original_invoice'];
            $invoice_no = $request['original_invoice_no'];
        }

        // Get location name
        $locationData = Location::find($location);
        $locationName = $locationData ? $locationData->location : null;

        // ✅ UPDATE MAIN TABLE
        Ripening_Chamber::where('id', $id)->update([
            'billdate' => $billDate,
            'farm_dcNo' => $request['farm_dcNo'],
            'receive_location_id' => $location,
            'receive_location_name' => $locationName,
            'Invoicenumber' => $invoiceNumber,
            'invoice_no' => $invoice_no,
            'season' => $season,
            'update_id' => Auth::id(),
            'updated_at' => now(),
        ]);

        // ✅ DELETE OLD DETAILS
        Ripening_Chamber_details::where('pid', $id)->delete();

        // ✅ INSERT NEW DETAILS
        $services = $request['services'] ?? [];
        $sizes = $request['size'] ?? [];
        $stages = $request['stage'] ?? [];
        $quantities = $request['Quantity'] ?? [];
        $batches = $request['batch_number'] ?? [];
        $receivedQty = $request['received_qty'] ?? [];
        $missingQty = $request['missing_qty'] ?? [];

        for ($i = 0; $i < count($services); $i++) {



    $received = $receivedQty[$i] ?? 0;
    $missing = $missingQty[$i] ?? 0;

    // Skip this row if both received and missing qty are 0
    if ($received == 0 && $missing == 0) {
        continue;
    }
            $sizeData = Product_details::find($sizes[$i]);
            $size_name = $sizeData ? $sizeData->product_size : null;

            Ripening_Chamber_details::create([
                'pid' => $id,
                'services' => $services[$i],
                'size' => $sizes[$i],
                'size_name' => $size_name,
                'stage' => $stages[$i] ?? null,
                'Quantity' => $quantities[$i] ?? 0,
                'batch_number' => $batches[$i] ?? null,
                'received_qty' => $receivedQty[$i] ?? 0,
                'missing_qty' => $missingQty[$i] ?? 0,
                'user_id' => Auth::id(),
                'update_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return redirect()->route('admin.ripening_chamber.index')
            ->with('success', __('Ripening Chamber Challan Updated Successfully'));

    } catch (\Exception $e) {
        DB::rollback();
        throw $e;
    }
}

    /**
     * Delete an inward record and its associated inward details.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $inward = $this->model->findOrFail($id);
            // Delete associated inward details
            Ripening_Chamber_details::where('pid', $inward->id)->delete();
            // Delete the inward record
            $inward->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Ripening Chamber Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
