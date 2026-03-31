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

// public function getRipeningChambers(Request $request)
// {
//     $data = \App\Models\Warehouse_inward::where('receive_location_id', $request->location_id)
//         ->select('Invoicenumber')
//         ->distinct()
//         ->get();

//     return response()->json([
//         'data' => $data
//     ]);
// }


public function getRipeningChambers(Request $request)
{
    $locationId = $request->location_id;

    // 🔹 Step 1: Get warehouse stock details
    $warehouse = DB::table('warehouse_inward_details as wid')
        ->join('warehouse_inward as wi', 'wi.id', '=', 'wid.pid')
        ->when($locationId, fn($q) => $q->where('wi.receive_location_id', $locationId))
        ->select(
            'wi.Invoicenumber',
            'wi.receive_location_id',
            'wid.services',
            'wid.size',
            'wid.stage',
            'wid.batch_number',
        DB::raw('SUM(wid.received_qty) as inward_qty')
        )
        ->groupBy(
            'wi.Invoicenumber',
            'wi.receive_location_id',
            'wid.services',
            'wid.size',
            'wid.stage',
            'wid.batch_number'
        )
        ->get();

    // 🔹 Step 2: Get outward quantities from ripening chamber
    $ripening = DB::table('ripening_chamber_details as rcd')
        ->join('ripening_chamber as rc', 'rc.id', '=', 'rcd.pid')
        ->select(
            'rc.warehouse_inward_No',
            'rc.receive_location_id',
            'rcd.services',
            'rcd.size',
            'rcd.stage',
            'rcd.batch_number',
            DB::raw('SUM(rcd.chamber_qty) as outward_qty')
        )
        ->groupBy(
            'rc.warehouse_inward_No',
            'rc.receive_location_id',
            'rcd.services',
            'rcd.size',
            'rcd.stage',
            'rcd.batch_number'
        )
        ->get();

    // 🔹 Step 3: Build ripening map
    $ripeningMap = [];
    foreach ($ripening as $r) {
        $key = $r->warehouse_inward_No . '_' . $r->receive_location_id . '_' . $r->services . '_' . $r->size . '_' . $r->stage . '_' . $r->batch_number;
        $ripeningMap[$key] = $r->outward_qty;
    }

    // 🔹 Step 4: Collect invoices with remaining stock > 0
    $invoiceSet = [];
    foreach ($warehouse as $w) {
        $key = $w->Invoicenumber . '_' . $w->receive_location_id . '_' . $w->services . '_' . $w->size . '_' . $w->stage . '_' . $w->batch_number;
        $outwardQty = $ripeningMap[$key] ?? 0;
        $remaining = max($w->inward_qty - $outwardQty, 0);

        if ($remaining > 0) {
            $invoiceSet[$w->Invoicenumber] = true; // use associative array to avoid duplicates
        }
    }

    $data = array_keys($invoiceSet); // only invoices with stock

    return response()->json([
        'data' => $data
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
        $remainingQty = $row->received_qty - $totalReceived;

        if ($remainingQty <= 0) continue;

        $data[] = [
            'services' => $product->product_name ?? '',
            'servicesid' => $row->services,
            'size' => $row->size,
            'p_size' => $size->product_size ?? '',
            'stage' => $row->stage,
            'batch_number' => $row->batch_number,
            'Quantity' => $row->received_qty,
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

 public function Ripeningstock()
    {
        $locations = DB::table('location')
            ->select('id', 'location')
            ->get();

        return view('admin.ripening_chamber.ripeningstock', compact('locations'));
    }

    // Get data for DataTable
//    public function getStock(Request $request)
// {
//     $locationId = $request->location_id;
//     $warehouseNo = $request->warehouse_inward_No;

//     // Fetch all inward details with product info
//     $inwardDetails = \App\Models\Warehouse_inward_details::select(
//         'warehouse_inward_details.id',
//         'warehouse_inward_details.pid',
//         'warehouse_inward_details.services',
//         'warehouse_inward_details.size',
//         'warehouse_inward_details.stage',
//         'warehouse_inward_details.batch_number',
//         'warehouse_inward_details.received_qty',
//         'warehouse_inward_details.missing_qty',
//         'product_details.product_size',
//         'products.product_name',
//         'warehouse_inward.receive_location_name',
//         'warehouse_inward.Invoicenumber'
//     )
//     ->join('warehouse_inward', 'warehouse_inward.id', '=', 'warehouse_inward_details.pid')
//     ->join('product_details', 'product_details.id', '=', 'warehouse_inward_details.size')
//     ->join('products', 'products.id', '=', 'warehouse_inward_details.services')
//     ->when($locationId, fn($q) => $q->where('warehouse_inward.receive_location_id', $locationId))
//     ->when($warehouseNo, fn($q) => $q->where('warehouse_inward.Invoicenumber', $warehouseNo))
//     ->get();

//     $data = [];

//     foreach ($inwardDetails as $row) {
//         // Total outward (ripening chamber) qty
//         $outwardQty = \App\Models\Ripening_Chamber_details::join('ripening_chamber', 'ripening_chamber.id', '=', 'ripening_chamber_details.pid')
//             ->where('ripening_chamber.warehouse_inward_No', $row->Invoicenumber)
//             ->where('ripening_chamber_details.services', $row->services)
//             ->where('ripening_chamber_details.size', $row->size)
//             ->where('ripening_chamber_details.stage', $row->stage)
//             ->where('ripening_chamber_details.batch_number', $row->batch_number)
//             ->sum('ripening_chamber_details.chamber_qty');

//         $currentStock = ($row->received_qty ?? 0) - ($outwardQty ?? 0) - ($row->missing_qty ?? 0);

//         $data[] = [
//             'warehouse_No'     => $row->Invoicenumber,
//             'location_name'    => $row->receive_location_name,
//             'service_name'     => $row->product_name,
//             'size_name'        => $row->product_size,
//             'stage'            => $row->stage,
//             'batch_number'     => $row->batch_number,
//             'inward_qty'       => $row->received_qty,
//             'outward_qty'      => $outwardQty,
//             'remaining_qty'    => $currentStock,
//         ];
//     }

//     return response()->json([
//         'status' => 'success',
//         'data' => $data
//     ]);
// }


public function getStock(Request $request)
{
    $locationId = $request->location_id;
    $warehouseNo = $request->warehouse_inward_No;

    // 🔹 Step 1: Warehouse Data
    $warehouse = \DB::table('warehouse_inward_details as wid')
        ->join('warehouse_inward as wi', 'wi.id', '=', 'wid.pid')
        ->join('products as p', 'p.id', '=', 'wid.services')
        ->join('product_details as pd', 'pd.id', '=', 'wid.size')

        ->when($locationId, fn($q) =>
            $q->where('wi.receive_location_id', $locationId)
        )

        ->when($warehouseNo, fn($q) =>
            $q->where('wi.Invoicenumber', $warehouseNo)
        )

        ->select(
            'wi.Invoicenumber',
            'wi.receive_location_id',
            'wi.receive_location_name',
            'wid.services',
            'wid.size',
            'wid.stage',
            'wid.batch_number',
            'p.product_name',
            'pd.product_size',
            \DB::raw('SUM(wid.received_qty) as inward_qty')
        )

        ->groupBy(
            'wi.Invoicenumber',
            'wi.receive_location_id',
            'wi.receive_location_name',
            'wid.services',
            'wid.size',
            'wid.stage',
            'wid.batch_number',
            'p.product_name',
            'pd.product_size'
        )
        ->get();

    // 🔹 Step 2: Ripening Data
    $ripening = \DB::table('ripening_chamber_details as rcd')
        ->join('ripening_chamber as rc', 'rc.id', '=', 'rcd.pid')

        ->select(
            'rc.warehouse_inward_No',
            'rc.receive_location_id',
            'rcd.services',
            'rcd.size',
            'rcd.stage',
            'rcd.batch_number',
            \DB::raw('SUM(rcd.chamber_qty) as outward_qty')
        )

        ->groupBy(
            'rc.warehouse_inward_No',
            'rc.receive_location_id',
            'rcd.services',
            'rcd.size',
            'rcd.stage',
            'rcd.batch_number'
        )
        ->get();

    // 🔹 Step 3: Convert ripening to key map
    $ripeningMap = [];

    foreach ($ripening as $r) {
        $key = $r->warehouse_inward_No . '_' .
               $r->receive_location_id . '_' .
               $r->services . '_' .
               $r->size . '_' .
               $r->stage . '_' .
               $r->batch_number;

        $ripeningMap[$key] = $r->outward_qty;
    }

    // 🔹 Step 4: Merge Data
    $data = [];

    foreach ($warehouse as $w) {

        $key = $w->Invoicenumber . '_' .
               $w->receive_location_id . '_' .
               $w->services . '_' .
               $w->size . '_' .
               $w->stage . '_' .
               $w->batch_number;

        $outwardQty = $ripeningMap[$key] ?? 0;
        $remaining = max($w->inward_qty - $outwardQty, 0);

        $data[] = [
            'warehouse_No'   => $w->Invoicenumber,
            'location_name'  => $w->receive_location_name,
            'service_name'   => $w->product_name,
            'size_name'      => $w->product_size,
            'stage'          => $w->stage,
            'batch_number'   => $w->batch_number,
            'inward_qty'     => $w->inward_qty,
            'outward_qty'    => $outwardQty,
            'remaining_qty'  => $remaining,
        ];
    }

    return response()->json([
        'status' => 'success',
        'data' => $data
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
