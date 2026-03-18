<?php

namespace App\Repositories;

use App\Models\farm_inward;
use App\Models\farm_inward_details;
use App\Models\Product_details;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class farm_inwardRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return farm_inward::class;
    }

    /**
     * Store a new inward record along with its details in the database.
     */
    public function store(Request $request)
    {

    $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');

    $season = session('selected_season');

     $location = $request->location_id;
     $supplier = $request->supplier;

    $supp = Supplier::find($supplier);
    $supplier_name =  $supp->supplier_name ;

    $locationname = Location::find($location);
    $loname = $locationname ? $locationname->location : null;

        $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmInward($request->location_id);
        $batch   = \App\Helpers\Helpers::getNextBatchForFarmInward($request->location_id);

        $batchNumber = $batch['formatted'];
        $invoiceNumber = $invoice['formatted'];

        DB::beginTransaction();
        try {
            $inward = [
                'user_id'    => Auth::id(),
                'PurchaseDate' => $billDate,
                'supplier'=> $supplier_name,
                'supplier_id'=> $request->supplier,
                'location_id'=> $request->location_id,
                'location_name'    =>$loname,
                'Tquantity'   => $request->totalproamt,
                'Invoicenumber' => $invoiceNumber,
                'invoice_no'    => $invoice['number'],
                 'update_id'   => Auth::id(),
                 'season'  => $season,
                'batch_no'      => $batch['number'],
                'batch_number'  => $batchNumber,
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),

            ];

                $inwardId = farm_inward::insertGetId($inward);

                $cnt = $request['cnt'];

                    for ($i = 1; $i <= $cnt; $i++) {
                        $service = $request['services_' . $i] ?? null;
                        $size = $request['size_' . $i] ?? null;
                        $stage = $request['stage_' . $i] ?? null;
                        $quantity = $request['quantity_' . $i] ?? 0;
                        $rate = $request['rate_' . $i] ?? 0;

                    $sizen = Product_details::find($size);
                    $size_name = $sizen->product_size ;
                        $inwardDetailData = [
                            'user_id'    => Auth::id(),
                            'pid'        => $inwardId,
                            'services'   => $service,
                            'size'       => $size,
                            'size_name'  => $size_name,
                            'stage'      => $stage,
                            'Quantity'   => $quantity,
                            'rate'       => $rate,
                            'batch_no'      => $batch['number'],
                            'batch_number'  => $batchNumber,
                            'update_id'  => Auth::id(),
                            'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        ];
                        farm_inward_details::create($inwardDetailData);
                    }


                            DB::commit();
                            return redirect()->route('admin.farm_inward.index')->with('success', __('Inward Created Successfully'));
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

        $location = $request['location_id'];
        $supplier = $request['supplier'];

        $supp = Supplier::find($supplier);
        $supplier_name = $supp->supplier_name;

        $locationname = Location::find($location);
        $loname = $locationname ? $locationname->location : null;

        // Check if location changed
        $originalLocation = $request['original_location_id'] ?? null;
        if ($location != $originalLocation) {
            $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmInward($location);
            $batch = \App\Helpers\Helpers::getNextBatchForFarmInward($location);

            $invoiceNumber = $invoice['formatted'];
            $batchNumber = $batch['formatted'];
            $invoice_no = $invoice['number'];
            $batch_no = $batch['number'];
        } else {
            // Keep original invoice & batch
            $invoiceNumber = $request['original_invoice'];
            $batchNumber = $request['original_batch'];
            $invoice_no = $request['original_invoice_no'];
            $batch_no = $request['original_batch_no'];
        }

        // Update farm_inward
        DB::table('farm_inward')->where('id', $id)->update([
            'PurchaseDate' => $billDate,
            'Tquantity' => $request['totalproamt'],
            'location_id' => $location,
            'location_name' => $loname,
            'supplier' => $supplier_name,
            'supplier_id' => $supplier,
            'update_id' => Auth::id(),
            'updated_at' => now(),
            'season' => $season,
            'Invoicenumber' => $invoiceNumber,
            'invoice_no' => $invoice_no,
            'batch_number' => $batchNumber,
            'batch_no' => $batch_no,
        ]);

        // Delete old details and insert new
        if (!empty($request['cnt'])) {
            farm_inward_details::where('pid', $id)->delete();

            $cnt = $request['cnt'];
            for ($i = 1; $i <= $cnt; $i++) {
                $service = $request['services_' . $i] ?? null;
                $size = $request['size_' . $i] ?? null;
                $stage = $request['stage_' . $i] ?? null;
                $quantity = $request['quantity_' . $i] ?? 0;
                $rate = $request['rate_' . $i] ?? 0;

                if ($service && $size) {
                    $sizen = Product_details::find($size);
                    $size_name = $sizen->product_size;

                    farm_inward_details::create([
                        'user_id' => Auth::id(),
                        'pid' => $id,
                        'services' => $service,
                        'size' => $size,
                        'size_name' => $size_name,
                        'stage' => $stage,
                        'Quantity' => $quantity,
                        'rate' => $rate,
                        'batch_no' => $batch_no,
                        'batch_number' => $batchNumber,
                        'update_id' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('admin.farm_inward.index')->with('success', __('Inward Updated Successfully'));
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
            farm_inward_details::where('pid', $inward->id)->delete();
            // Delete the inward record
            $inward->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Inward Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
