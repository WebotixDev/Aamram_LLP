<?php

namespace App\Repositories;

use App\Models\Warehouse_inward;
use App\Models\Warehouse_inward_details;
use App\Models\Product_details;
use App\Models\Location;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class WarehouseInwardRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Warehouse_inward::class;
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
                'farm_dcNo'=> $request->farm_dcNo,
                'receive_location_id'=> $request->receive_location_id,
                'receive_location_name'    =>$loname,
                'Invoicenumber' => $invoiceNumber,
                'invoice_no'    => $invoice['number'],
                 'update_id'   => Auth::id(),
                 'season'  => $season,
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),

            ];

                $inwardId = Warehouse_inward::insertGetId($inward);

               $services = $request->services;
                $sizes = $request->size;
                $stages = $request->stage;
                $quantities = $request->Quantity;
                $batches = $request->batch_number;
                $receivedQty = $request->received_qty;
                $missingqty = $request->missing_qty ?? [];

                for ($i = 0; $i < count($services); $i++) {

    $received = $receivedQty[$i] ?? 0;
    $missing = $missingQty[$i] ?? 0;

    if ($received == 0 && $missing == 0) {
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
                        'received_qty' => $receivedQty[$i] ?? 0,
                    'missing_qty' => $missingqty[$i] ?? 0, // ✅ SAFE
                        'update_id'  => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    Warehouse_inward_details::create($inwardDetailData);
                }


                            DB::commit();
                            return redirect()->route('admin.warehouse_inward.index')->with('success', __('Warehouse Inward Created Successfully'));
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
            $invoice = \App\Helpers\Helpers::getNextInvoiceForWarehouseInward($location);
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
        Warehouse_inward::where('id', $id)->update([
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
        Warehouse_inward_details::where('pid', $id)->delete();

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

            Warehouse_inward_details::create([
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

        return redirect()->route('admin.warehouse_inward.index')
            ->with('success', __('Warehouse Inward Challan Updated Successfully'));

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
            Warehouse_inward_details::where('pid', $inward->id)->delete();
            // Delete the inward record
            $inward->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Warehouse Inward Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
