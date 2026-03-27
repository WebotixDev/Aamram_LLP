<?php

namespace App\Http\Controllers\inward;

use App\Models\Ripening_Chamber;
use App\Models\Ripening_Chamber_details;
use App\Models\Warehouse_inward;
use App\Models\Warehouse_inward_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\Ripening_ChamberDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\RipeningChamberRepository;
use Illuminate\Support\Facades\DB;

class RipeningChamberController extends Controller
{
    protected $repository;

    public function __construct(RipeningChamberRepository $repository)
    {
        $this->repository = $repository;
    }


    public function index(Ripening_ChamberDataTable $dataTable)
    {
        return $dataTable->render('admin.ripening_chamber.index');
    }


    public function create(Warehouse_inward $ripening_chamber)
    {

        return view('admin.ripening_chamber.create' , ['ripening_chamber' => $ripening_chamber]);

    }

public function getInvoiceBatch(Request $request)
{
    $invoice = \App\Helpers\Helpers::getNextInvoiceForRipening($request->location_id);

    return response()->json([
        'invoice' => $invoice,
    ]);
}
public function getBatchByLocation(Request $request)
{
    $location_id = $request->location_id;

    $batches = DB::table('farm_inward')
        ->where('location_id', $location_id)
        ->select('id','batch_number')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $batches
    ]);
}

public function WarehouseStockReport()
{
    $locations = DB::table('location')
        ->select('id', 'location')
        ->get();

    return view('admin.warehouse_inward.Warehousestock', compact('locations'));
}


public function getBatchByLocationForStock(Request $request)
{
    $location_id = $request->location_id;

    $batches = DB::table('farm_inward')
        ->where('location_id', $location_id)
        ->select('batch_number')
        ->distinct()
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $batches
    ]);
}


public function getOrderRecords(Request $request)
{
    $warehouse_inward_No = $request->warehouse_inward_No;

    $challan = Warehouse_inward::where('Invoicenumber', $warehouse_inward_No)
                ->orWhere('invoice_no', $warehouse_inward_No)
                ->first();

    if (!$challan) {
        return response()->json(['status' => 'error','data' => []]);
    }

    $details = Warehouse_inward_details::where('pid', $challan->id)->get();
    $data = [];

    foreach ($details as $row) {
        $product = DB::table('products')->where('id', $row->services)->first();
        $size = DB::table('product_details')->where('id', $row->size)->first();

        // Sum of received + missing from all inward details **excluding current inward row if editing**
        $totalReceived = DB::table('ripening_chamber_details as wid')
            ->join('ripening_chamber as wi','wi.id','=','wid.pid')
            ->where('wi.warehouse_inward_No', $warehouse_inward_No)
            ->where('wid.services', $row->services)
            ->where('wid.size', $row->size)
            ->where('wid.batch_number', $row->batch_number)
            ->when($request->inward_id, function($q) use ($request) {
                $q->where('wid.pid', '!=', $request->inward_id);
            })
            ->sum(DB::raw('wid.chamber_qty'));

        // Current row values for edit mode
        $currentEditReceived = 0;

        if ($request->inward_id) {
            $currentRow = DB::table('ripening_chamber_details')
                ->where('pid', $request->inward_id)
                ->where('services', $row->services)
                ->where('size', $row->size)
                ->where('batch_number', $row->batch_number)
                ->first();

            $currentEditReceived = $currentRow->chamber_qty ?? 0;
        }

        // ✅ Remaining quantity = original quantity - sum(received + missing from other inwards)
        $remainingQty = $row->Quantity - $totalReceived;

        if ($remainingQty <= 0) continue;

        $data[] = [
            'services' => $product->product_name ?? '',
            'servicesid' => $row->services,
            'size' => $row->size,
            'p_size' => $size->product_size ?? '',
            'stage' => $row->stage,
            'batch_number' => $row->batch_number,
            'Quantity' => $row->Quantity,
            'rem_qty' => $remainingQty,
            'chamber_qty' => $currentEditReceived,
        ];
    }

    if (empty($data)) {
        return response()->json([
            'status' => 'empty',
            'message' => 'All quantities already received for this challan.',
            'data' => []
        ]);
    }

    return response()->json(['status' => 'success','data' => $data]);
}

public function stockReport(Request $request)
{
    $locationId = $request->location_id;
    $farmDcNo   = $request->farm_dcNo;

    $records = DB::table('farm_delivery_challan_details as fdc')
        ->join('farm_delivery_challan as fc', 'fc.id', '=', 'fdc.pid')

        ->leftJoin('warehouse_inward_details as wid', function ($join) {
            $join->on('wid.services', '=', 'fdc.services')
                 ->on('wid.size', '=', 'fdc.size')
                 ->on('wid.batch_number', '=', 'fdc.batch_number');
        })

        ->leftJoin('warehouse_inward as wi', 'wi.id', '=', 'wid.pid')

        ->leftJoin('products as p', 'p.id', '=', 'fdc.services')
        ->leftJoin('product_details as pd', 'pd.id', '=', 'fdc.size')

        ->select(
            'wi.receive_location_name',
            'fc.Invoicenumber as farm_dcNo',
            'p.product_name',
            'pd.product_size',
            'fdc.stage',
            'fdc.batch_number',

            DB::raw('SUM(fdc.Quantity) as challan_qty'),
            DB::raw('COALESCE(SUM(wid.received_qty),0) as total_received'),
            DB::raw('COALESCE(SUM(wid.missing_qty),0) as total_missing'),

            DB::raw('(SUM(fdc.Quantity) - COALESCE(SUM(wid.received_qty + wid.missing_qty),0)) as remaining_qty')
        )

        ->when($locationId, function ($q) use ($locationId) {
            $q->where('wi.receive_location_id', $locationId);
        })

        ->when($farmDcNo, function ($q) use ($farmDcNo) {
            $q->where('fc.Invoicenumber', $farmDcNo);
        })

        ->groupBy(
            'wi.receive_location_name',
            'fc.Invoicenumber',
            'p.product_name',
            'pd.product_size',
            'fdc.stage',
            'fdc.batch_number'
        )
        ->get();

    $data = [];

    foreach ($records as $row) {
        $data[] = [
            'location_name'   => $row->receive_location_name ?? 'N/A',
            'farm_dcNo'       => $row->farm_dcNo,
            'service_name'    => $row->product_name,
            'size_name'       => $row->product_size,
            'stage'           => $row->stage,
            'batch_number'    => $row->batch_number,
            'challan_qty'     => $row->challan_qty,
            'total_received'  => $row->total_received,
            'total_missing'   => $row->total_missing,
            'remaining_qty'   => $row->remaining_qty,
        ];
    }

    return response()->json([
        'status' => 'success',
        'data'   => $data
    ]);
}
    /**
     * Store a newly created inward record and its details.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }


public function getdetails(Request $request)
{
    $services_id = $request->services;

    // Check product
    $product = Product::find($services_id);

    if(!$product){
        return response()->json([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
    }

    // Fetch sizes
    $sizes = Product_details::where('parentID',$product->id)
                ->where('status',0)
                ->where('sizeoff',0)
                ->where('disable','!=',1)
                ->get(['id','product_size']);

    // If no sizes
    if($sizes->isEmpty()){
        return response()->json([
            'status' => 'nosize',
            'data' => []
        ]);
    }

    return response()->json([
        'status' => 'success',
        'data' => $sizes
    ]);
}

public function getStock(Request $request)
{

    $service = $request->service;
    $size = $request->size;
    $stage = $request->stage;
    $batch_number = $request->batch_number;

    $stock = \App\Helpers\Helpers::getFarmStock($service, $size, $stage, $batch_number);

    return response()->json([
        'status' => 'success',
        'stock' => $stock
    ]);
}
    /**
     * Display the specified inward record along with its details.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    // public function show($id)
    // {
    //     $farm_inward = Farm_Delivery_challan::with('details')->findOrFail($id);
    //     return view('admin.Farm_Delivery_challan.show', compact('farm_inward'));
    // }


    /**
     * Show the form for editing the inward record.
     *
     * @param farm_inward $inward
     * @return \Illuminate\View\View
     */

    public function edit(Ripening_Chamber $ripening_chamber)
    {


        $invoice = DB::table('ripening_chamber')->where('id',  $ripening_chamber->id)->first();

        $ripening_chamber = Ripening_Chamber::where('id', $ripening_chamber->id)->first();

        $ripening_chamber_details = Ripening_Chamber_details::where('pid', $ripening_chamber->id)->get();

        return view('admin.ripening_chamber.edit', ['ripening_chamber' => $ripening_chamber],compact('ripening_chamber','ripening_chamber_details','invoice'));

    }

    /**
     * Update the specified inward record and its details.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Ripening_Chamber $ripening_chamber)
    {
        return $this->repository->update($request->all(), $ripening_chamber->id);
    }

    /**
     * Remove the specified inward record and its details from storage.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Ripening_Chamber $ripening_chamber)
    {
        return $this->repository->destroy($ripening_chamber->id);
    }
    public function getData(Ripening_ChamberDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function FarmDCBill(Request $request)
{
    $id = $request->query('id'); // or $request->get('id')
        // Fetch sale order, sale order details, and customer details using joins
        $farm_delivery_challan = DB::table('farm_delivery_challan')
            ->where('farm_delivery_challan.id', $id)
            ->leftJoin('farm_delivery_challan_details', 'farm_delivery_challan.id', '=', 'farm_delivery_challan_details.pid')
            ->select(
                'farm_delivery_challan_details.*',
                'farm_delivery_challan.*',
            )
            ->first();

        // Fetch the sale order details separately
        $farm_delivery_challan_details = DB::table('farm_delivery_challan_details')
        ->where('farm_delivery_challan_details.pid', $farm_delivery_challan->id)
        ->leftJoin('products', 'farm_delivery_challan_details.services', '=', 'products.id')
        ->select('farm_delivery_challan_details.*', 'products.product_name') // Selecting necessary fields
        ->get();


        return view('admin.Farm_Delivery_challan.farmDC', compact('farm_delivery_challan', 'farm_delivery_challan_details'));
    }
}
