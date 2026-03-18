<?php

namespace App\Repositories;

use App\Models\Farm_Delivery_challan;
use App\Models\Farm_Delivery_challan_details;
use App\Models\Product_details;
use App\Models\Location;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;

class Farm_deliveryChallanRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Farm_Delivery_challan::class;
    }

    /**
     * Store a new inward record along with its details in the database.
     */
    public function store(Request $request)
    {

    $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');

    $season = session('selected_season');

     $location = $request->from_location_id;
    $to_location = $request->to_location_id;

    $transporter = $request->transporter_id;



    $transporter = Transporter::find($transporter);
    $transporter_name = $transporter ? $transporter->transporter : null;


    $locationname = Location::find($location);
    $loname = $locationname ? $locationname->location : null;


    $to_location = Location::find($to_location);
    $to_loname = $to_location ? $to_location->location : null;

        $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmDC($request->from_location_id);

        $invoiceNumber = $invoice['formatted'];

        DB::beginTransaction();
        try {
            $inward = [
                'user_id'    => Auth::id(),
                'challan_date' => $billDate,
                'transporter_id'=> $request->transporter_id,
                'transporter_name'=> $transporter_name,
                'from_location_id'=> $request->from_location_id,
                'from_location_name'    =>$loname,
                'to_location_id'=> $request->to_location_id,
                'to_location_name'    =>$to_loname,
                'driver_name'   => $request->driver_name,
                'driver_mobile_no'   => $request->driver_mobile_no,
                'Invoicenumber' => $invoiceNumber,
                'invoice_no'    => $invoice['number'],
                 'update_id'   => Auth::id(),
                 'season'  => $season,
                 'totalamt'   => $request->totalamt,
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),

            ];

                $inwardId = Farm_Delivery_challan::insertGetId($inward);

                $cnt = $request['cnt'];

                    for ($i = 1; $i <= $cnt; $i++) {
                        $service = $request['services_' . $i] ?? null;
                        $size = $request['size_' . $i] ?? null;
                        $stage = $request['stage_' . $i] ?? null;
                        $quantity = $request['quantity_' . $i] ?? 0;
                        $batch_number = $request['batch_number_' . $i] ?? 0;
                        $transcost = $request['transcost_' . $i] ?? 0;

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
                            'batch_number'  => $batch_number,
                            'transcost'  => $transcost,
                            'update_id'  => Auth::id(),
                            'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        ];
                        Farm_Delivery_challan_details::create($inwardDetailData);
                    }


                            DB::commit();
                            return redirect()->route('admin.Farm_Delivery_challan.index')->with('success', __('Farm Delivery Challan Created Successfully'));
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


    $location = $request['from_location_id'];
    $to_location = $request['to_location_id'];

    $transporter = $request['transporter_id'];



    $transporter = Transporter::find($transporter);
    $transporter_name = $transporter ? $transporter->transporter : null;


    $locationname = Location::find($location);
    $loname = $locationname ? $locationname->location : null;


    $to_location = Location::find($to_location);
    $to_loname = $to_location ? $to_location->location : null;

        // Check if location changed
        $originalLocation = $request['original_location_id'] ?? null;
        if ($location != $originalLocation) {
            $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmDC($location);

            $invoiceNumber = $invoice['formatted'];
            $invoice_no = $invoice['number'];
        } else {
            // Keep original invoice & batch
            $invoiceNumber = $request['original_invoice'];
            $invoice_no = $request['original_invoice_no'];
        }

        // Update farm_inward
        DB::table('farm_delivery_challan')->where('id', $id)->update([
                     'user_id'    => Auth::id(),
                'challan_date' => $billDate,
                'transporter_id'=> $request['transporter_id'],
                'transporter_name'=> $transporter_name,
                'from_location_id'=> $request['from_location_id'],
                'from_location_name'    =>$loname,
                'to_location_id'=> $request['to_location_id'],
                'to_location_name'    =>$to_loname,
                'driver_name'   => $request['driver_name'],
                'driver_mobile_no'   => $request['driver_mobile_no'],
                'totalamt'   => $request['totalamt'],
                 'update_id'   => Auth::id(),
                 'season'  => $season,
                'Invoicenumber' => $invoiceNumber,
                'invoice_no' => $invoice_no,
        ]);

        // Delete old details and insert new
        if (!empty($request['cnt'])) {
            Farm_Delivery_challan_details::where('pid', $id)->delete();

            $cnt = $request['cnt'];
            for ($i = 1; $i <= $cnt; $i++) {
                 $service = $request['services_' . $i] ?? null;
                        $size = $request['size_' . $i] ?? null;
                        $stage = $request['stage_' . $i] ?? null;
                        $quantity = $request['quantity_' . $i] ?? 0;
                        $batch_number = $request['batch_number_' . $i] ?? 0;
                        $transcost = $request['transcost_' . $i] ?? 0;

                if ($service && $size) {
                    $sizen = Product_details::find($size);
                    $size_name = $sizen->product_size;

                    Farm_Delivery_challan_details::create([
                        'user_id' => Auth::id(),
                        'pid' => $id,
                        'services' => $service,
                        'size' => $size,
                        'size_name' => $size_name,
                        'stage' => $stage,
                        'Quantity' => $quantity,
                        'batch_number' => $batch_number,
                      'transcost'  => $transcost,
                        'update_id' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('admin.Farm_Delivery_challan.index')->with('success', __('Farm Delivery Challan Updated Successfully'));
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
            Farm_Delivery_challan_details::where('pid', $inward->id)->delete();
            // Delete the inward record
            $inward->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Farm Delivery Challan Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
