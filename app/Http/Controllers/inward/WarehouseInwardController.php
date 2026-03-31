<?php

namespace App\Http\Controllers\inward;

use App\Models\Farm_Delivery_challan;
use App\Models\Farm_Delivery_challan_details;
use App\Models\Warehouse_inward;
use App\Models\Warehouse_inward_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\WarehouseInwardDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\WarehouseInwardRepository;
use Illuminate\Support\Facades\DB;

class WarehouseInwardController extends Controller
{
    protected $repository;

    public function __construct(WarehouseInwardRepository $repository)
    {
        $this->repository = $repository;
    }


    public function index(WarehouseInwardDataTable $dataTable)
    {
        return $dataTable->render('admin.warehouse_inward.index');
    }


    public function create(Warehouse_inward $Warehouse_inward)
    {

        return view('admin.warehouse_inward.create' , ['Warehouse_inward' => $Warehouse_inward]);

    }

public function getInvoiceBatch(Request $request)
{
    $invoice = \App\Helpers\Helpers::getNextInvoiceForWarehouseInward($request->location_id);

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

// public function getOrderRecords(Request $request)
// {
//     $farm_dcNo = $request->farm_dcNo;

//     $challan = Farm_Delivery_challan::where('Invoicenumber',$farm_dcNo)
//                 ->orWhere('invoice_no',$farm_dcNo)
//                 ->first();

//     if(!$challan){
//         return response()->json(['status'=>'error','data'=>[]]);
//     }

//     $details = Farm_Delivery_challan_details::where('pid',$challan->id)->get();

//     $data = [];

//     // foreach($details as $row){
//     //     $product = DB::table('products')->where('id',$row->services)->first();
//     //     $size = DB::table('product_details')->where('id',$row->size)->first();

//     //     $receivedSum = DB::table('warehouse_inward_details as wid')
//     //         ->join('warehouse_inward as wi','wi.id','=','wid.pid')
//     //         ->where('wi.farm_dcNo',$farm_dcNo)
//     //         ->where('wid.services',$row->services)
//     //         ->where('wid.size',$row->size)
//     //         ->where('wid.batch_number',$row->batch_number)
//     //         ->sum('wid.received_qty');

//     //     $currentEditReceived = 0;
//     //     if($request->inward_id){
//     //         $currentEditReceived = DB::table('warehouse_inward_details')
//     //             ->where('pid',$request->inward_id)
//     //             ->where('services',$row->services)
//     //             ->where('size',$row->size)
//     //             ->where('batch_number',$row->batch_number)
//     //             ->value('received_qty') ?? 0;
//     //     }

//     //     $remainingQty = $row->Quantity - $receivedSum + $currentEditReceived;
//     //     if($remainingQty <= 0) continue;

//     //     $data[] = [
//     //         'services' => $product->product_name ?? '',
//     //         'servicesid' => $row->services,
//     //         'size' => $row->size,
//     //         'p_size' => $size->product_size ?? '',
//     //         'stage' => $row->stage,
//     //         'batch_number' => $row->batch_number,
//     //         'Quantity' => $row->Quantity,
//     //         'rem_qty' => $remainingQty,
//     //         'received_qty' => $currentEditReceived
//     //     ];
//     // }

//     foreach($details as $row){
//     $product = DB::table('products')->where('id',$row->services)->first();
//     $size = DB::table('product_details')->where('id',$row->size)->first();

//     // Sum of only received quantities from warehouse_inward_details
//     $receivedSum = DB::table('warehouse_inward_details as wid')
//         ->join('warehouse_inward as wi','wi.id','=','wid.pid')
//         ->where('wi.farm_dcNo',$farm_dcNo)
//         ->where('wid.services',$row->services)
//         ->where('wid.size',$row->size)
//         ->where('wid.batch_number',$row->batch_number)
//         ->sum('wid.received_qty');

//     $currentEditReceived = 0;
//     $currentEditMissing = 0;

//     if($request->inward_id){
//         $currentRow = DB::table('warehouse_inward_details')
//             ->where('pid',$request->inward_id)
//             ->where('services',$row->services)
//             ->where('size',$row->size)
//             ->where('batch_number',$row->batch_number)
//             ->first();

//         $currentEditReceived = $currentRow->received_qty ?? 0;
//         $currentEditMissing = $currentRow->missing_qty ?? 0;
//     }

//     // Remaining quantity = original qty - received qty - missing qty already recorded + current edit
//     $remainingQty = $row->Quantity - $receivedSum - $currentEditMissing + $currentEditReceived;

//     if($remainingQty <= 0) continue;

//     $data[] = [
//         'services' => $product->product_name ?? '',
//         'servicesid' => $row->services,
//         'size' => $row->size,
//         'p_size' => $size->product_size ?? '',
//         'stage' => $row->stage,
//         'batch_number' => $row->batch_number,
//         'Quantity' => $row->Quantity,
//         'rem_qty' => $remainingQty,
//         'received_qty' => $currentEditReceived,
//         'missing_qty' => $currentEditMissing
//     ];
// }
//     if(empty($data)){
//         return response()->json(['status'=>'empty','message'=>'All quantities already received for this challan.','data'=>[]]);
//     }

//     return response()->json(['status'=>'success','data'=>$data]);
// }

public function getOrderRecords(Request $request)
{
    $farm_dcNo = $request->farm_dcNo;

      $location_id = $request->location_id; // ✅ get location
    // ✅ Check challan with location
    $challan = Farm_Delivery_challan::where(function($q) use ($farm_dcNo) {
            $q->where('Invoicenumber', $farm_dcNo)
              ->orWhere('invoice_no', $farm_dcNo);
        })
        ->where('to_location_id', $location_id) // ✅ filter by location
        ->first();

    // ❌ If not matching location
    if (!$challan) {
        return response()->json([
            'status' => 'invalid_location',
            'message' => 'This challan does not belong to selected location',
            'data' => []
        ]);
    }

    $details = Farm_Delivery_challan_details::where('pid', $challan->id)->get();
    $data = [];

    foreach ($details as $row) {
        $product = DB::table('products')->where('id', $row->services)->first();
        $size = DB::table('product_details')->where('id', $row->size)->first();

        // Sum of received + missing from all inward details **excluding current inward row if editing**
        $totalReceived = DB::table('warehouse_inward_details as wid')
            ->join('warehouse_inward as wi','wi.id','=','wid.pid')
            ->where('wi.farm_dcNo', $farm_dcNo)
            ->where('wid.services', $row->services)
            ->where('wid.size', $row->size)
            ->where('wid.batch_number', $row->batch_number)
            ->when($request->inward_id, function($q) use ($request) {
                $q->where('wid.pid', '!=', $request->inward_id);
            })
            ->sum(DB::raw('wid.received_qty + wid.missing_qty'));

        // Current row values for edit mode
        $currentEditReceived = 0;
        $currentEditMissing = 0;

        if ($request->inward_id) {
            $currentRow = DB::table('warehouse_inward_details')
                ->where('pid', $request->inward_id)
                ->where('services', $row->services)
                ->where('size', $row->size)
                ->where('batch_number', $row->batch_number)
                ->first();

            $currentEditReceived = $currentRow->received_qty ?? 0;
            $currentEditMissing = $currentRow->missing_qty ?? 0;
        }

        // ✅ Remaining quantity = original quantity - sum(received + missing from other inwards)
        $remainingQty = $row->Quantity - $totalReceived;

        // Skip if remaining <= 0
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
            'received_qty' => $currentEditReceived,
            'missing_qty' => $currentEditMissing,
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
// ==========================
// 👉 Get Stock Report
// ==========================

public function WarehouseStockReport()
{
    $locations = DB::table('location')
        ->select('id', 'location')
        ->get();

    return view('admin.warehouse_inward.Warehousestock', compact('locations'));
}

// public function stockReport(Request $request)
// {
//     $locationId = $request->location_id;
//     $farmDcNo   = $request->farm_dcNo;

//     // ✅ Step 1: Aggregate inward WITH farm_dcNo from HEADER (wi)
//     $inwardSub = DB::table('warehouse_inward_details as wid')
//         ->join('warehouse_inward as wi', 'wi.id', '=', 'wid.pid')
//         ->select(
//             'wi.receive_location_id',
//             'wi.receive_location_name',
//             'wi.farm_dcNo', // ✅ FROM HEADER TABLE

//             'wid.services',
//             'wid.size',
//             'wid.batch_number',

//             DB::raw('SUM(wid.received_qty) as total_received'),
//             DB::raw('SUM(wid.missing_qty) as total_missing')
//         )
//         ->groupBy(
//             'wi.receive_location_id',
//             'wi.receive_location_name',
//             'wi.farm_dcNo', // ✅ IMPORTANT

//             'wid.services',
//             'wid.size',
//             'wid.batch_number'
//         );

//     // ✅ Step 2: Main Query
//     $records = DB::table('farm_delivery_challan_details as fdc')
//         ->join('farm_delivery_challan as fc', 'fc.id', '=', 'fdc.pid')

//         ->leftJoinSub($inwardSub, 'wi_sum', function ($join) {
//             $join->on('wi_sum.services', '=', 'fdc.services')
//                  ->on('wi_sum.size', '=', 'fdc.size')
//                  ->on('wi_sum.batch_number', '=', 'fdc.batch_number')
//                  ->on('wi_sum.farm_dcNo', '=', 'fc.Invoicenumber'); // ✅ FINAL FIX
//         })

//         ->leftJoin('products as p', 'p.id', '=', 'fdc.services')
//         ->leftJoin('product_details as pd', 'pd.id', '=', 'fdc.size')

//         ->select(
//             'wi_sum.receive_location_name',
//             'fc.Invoicenumber as farm_dcNo',
//             'p.product_name',
//             'pd.product_size',
//             'fdc.stage',
//             'fdc.batch_number',

//             DB::raw('SUM(fdc.Quantity) as challan_qty'),
//             DB::raw('COALESCE(wi_sum.total_received,0) as total_received'),
//             DB::raw('COALESCE(wi_sum.total_missing,0) as total_missing'),

//             DB::raw('(SUM(fdc.Quantity) - COALESCE(wi_sum.total_received + wi_sum.total_missing,0)) as remaining_qty')
//         )

//         // ✅ Filters
//         ->when($locationId, function ($q) use ($locationId) {
//             $q->where('wi_sum.receive_location_id', $locationId);
//         })

//         ->when($farmDcNo, function ($q) use ($farmDcNo) {
//             $q->where('fc.Invoicenumber', $farmDcNo);
//         })

//         // ✅ Group By
//         ->groupBy(
//             'wi_sum.receive_location_name',
//             'wi_sum.total_received',
//             'wi_sum.total_missing',
//             'fc.Invoicenumber',
//             'p.product_name',
//             'pd.product_size',
//             'fdc.stage',
//             'fdc.batch_number'
//         )

//         ->orderBy('fc.Invoicenumber')
//         ->get();

//     // ✅ Format response
//     $data = [];

//     foreach ($records as $row) {
//         $data[] = [
//             'location_name'   => $row->receive_location_name ?? 'N/A',
//             'farm_dcNo'       => $row->farm_dcNo,
//             'service_name'    => $row->product_name,
//             'size_name'       => $row->product_size,
//             'stage'           => $row->stage,
//             'batch_number'    => $row->batch_number,
//             'challan_qty'     => (int) $row->challan_qty,
//             'total_received'  => (int) $row->total_received,
//             'total_missing'   => (int) $row->total_missing,
//             'remaining_qty'   => (int) $row->remaining_qty,
//         ];
//     }

//     return response()->json([
//         'status' => 'success',
//         'data'   => $data
//     ]);
// }


public function stockReport(Request $request)
{
    $locationId = $request->location_id;
    $farmDcNo   = $request->farm_dcNo;

    // Step 1: Get DC records filtered by DC number and location
    $dcRecords = DB::table('farm_delivery_challan_details as fdc')
        ->join('farm_delivery_challan as fc', 'fc.id', '=', 'fdc.pid')
        ->join('products as p', 'p.id', '=', 'fdc.services')
        ->join('product_details as pd', 'pd.id', '=', 'fdc.size')
        ->select(
            'fc.Invoicenumber as farm_dcNo',
            'fc.to_location_id',
            'fdc.services',
            'fdc.size',
            'p.product_name',
            'pd.product_size',
            'fdc.stage',
            'fdc.batch_number',
            'fdc.Quantity as challan_qty'
        )
        ->when($farmDcNo, function ($q) use ($farmDcNo) {
            $q->where('fc.Invoicenumber', $farmDcNo);
        })
        ->when($locationId, function ($q) use ($locationId) {
            $q->where('fc.to_location_id', $locationId);
        })
        ->get();

    $data = [];

foreach ($dcRecords as $row) {
    // Step 2: Sum received and missing separately
    $totals = DB::table('warehouse_inward_details as wid')
        ->join('warehouse_inward as wi', 'wi.id', '=', 'wid.pid')
        ->where('wi.farm_dcNo', $row->farm_dcNo)
        ->where('wi.receive_location_id', $row->to_location_id)
        ->where('wid.services', $row->services)
        ->where('wid.size', $row->size)
        ->where('wid.batch_number', $row->batch_number)
        ->select(
            DB::raw('COALESCE(SUM(wid.received_qty),0) as total_received'),
            DB::raw('COALESCE(SUM(wid.missing_qty),0) as total_missing')
        )
        ->first();

    $total_received = $totals->total_received ?? 0;
    $total_missing  = $totals->total_missing ?? 0;

    $remainingQty = $row->challan_qty - ($total_received + $total_missing);

    // ✅ Skip rows where both missing and remaining are 0
    if ($total_missing == 0 && $remainingQty == 0) continue;

    $location = DB::table('location')->where('id', $row->to_location_id)->value('location') ?? 'N/A';

    $data[] = [
        'location_name'   => $location, // friendly location name
        'farm_dcNo'       => $row->farm_dcNo,
        'service_name'    => $row->product_name,
        'size_name'       => $row->product_size,
        'stage'           => $row->stage,
        'batch_number'    => $row->batch_number,
        'challan_qty'     => (int) $row->challan_qty,
        'total_received'  => (int) $total_received,
        'total_missing'   => (int) $total_missing,
        'remaining_qty'   => (int) $remainingQty
    ];
}

    if (empty($data)) {
        return response()->json([
            'status' => 'empty',
            'message' => 'No stock available or all quantities already received for this DC.',
            'data' => []
        ]);
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

    public function edit(Warehouse_inward $Warehouse_inward)
    {


        $invoice = DB::table('warehouse_inward')->where('id',  $Warehouse_inward->id)->first();

        $Warehouse_inward = Warehouse_inward::where('id', $Warehouse_inward->id)->first();

        $Warehouse_inward_details = Warehouse_inward_details::where('pid', $Warehouse_inward->id)->get();

        return view('admin.warehouse_inward.edit', ['Warehouse_inward' => $Warehouse_inward],compact('Warehouse_inward','Warehouse_inward_details','invoice'));

    }

    /**
     * Update the specified inward record and its details.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Warehouse_inward $Warehouse_inward)
    {
        return $this->repository->update($request->all(), $Warehouse_inward->id);
    }

    /**
     * Remove the specified inward record and its details from storage.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Warehouse_inward $Warehouse_inward)
    {
        return $this->repository->destroy($Warehouse_inward->id);
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
