<?php

namespace App\Http\Controllers\inward;

use App\Models\Farm_Delivery_challan;
use App\Models\Farm_Delivery_challan_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\Farm_DeliveryChallanDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Farm_deliveryChallanRepository;
use Illuminate\Support\Facades\DB;

class WarehouseInwardController extends Controller
{
    protected $repository;

    public function __construct(Farm_deliveryChallanRepository $repository)
    {
        $this->repository = $repository;
    }


    public function index(Farm_DeliveryChallanDataTable $dataTable)
    {
        return $dataTable->render('admin.Farm_Delivery_challan.index');
    }


    public function create(Farm_Delivery_challan $Farm_Delivery_challan)
    {

        return view('admin.Farm_Delivery_challan.create' , ['Farm_Delivery_challan' => $Farm_Delivery_challan]);

    }

public function getInvoiceBatch(Request $request)
{
    $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmDC($request->location_id);

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

public function farmStockReport()
{
    $locations = DB::table('location')
        ->select('id', 'location')
        ->get();

    return view('admin.Farm_Delivery_challan.Farmstock', compact('locations'));
}

// ==========================
// 👉 Get Batch
// ==========================
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

// ==========================
// 👉 Get Stock Report
// ==========================
public function getStockReport(Request $request)
{
    $location_id = $request->location_id;
    $batch_number = $request->batch_number;

    // 1️⃣ Inward sums with product and size names
    $inwards = DB::table('farm_inward_details as fid')
        ->join('farm_inward as fi', 'fid.pid', '=', 'fi.id')
        ->leftJoin('products as p', 'p.id', '=', 'fid.services')
        ->leftJoin('product_details as pd', 'pd.id', '=', 'fid.size')
        ->where('fi.location_id', $location_id)
        ->when($batch_number, fn($q) => $q->where('fid.batch_number', $batch_number))
        ->groupBy('fid.batch_number', 'fid.services', 'fid.size', 'fid.stage', 'p.product_name', 'pd.product_size')
        ->select(
            'fid.batch_number',
            'fid.services',
            'fid.size',
            'fid.stage',
            'p.product_name as service_name',
            'pd.product_size as size_name',
            DB::raw('SUM(fid.Quantity) as inward_qty')
        )
        ->get();

    // 2️⃣ Outward sums
    $outwards = DB::table('farm_delivery_challan_details as fcd')
        ->join('farm_delivery_challan as fc', 'fcd.pid', '=', 'fc.id')
        ->where('fc.from_location_id', $location_id)
        ->when($batch_number, fn($q) => $q->where('fcd.batch_number', $batch_number))
        ->groupBy('fcd.batch_number', 'fcd.services', 'fcd.size', 'fcd.stage')
        ->select(
            'fcd.batch_number',
            'fcd.services',
            'fcd.size',
            'fcd.stage',
            DB::raw('SUM(fcd.Quantity) as outward_qty')
        )
        ->get()
        ->keyBy(fn($item) => "{$item->batch_number}-{$item->services}-{$item->size}-{$item->stage}");

    // 3️⃣ Merge inward and outward to calculate stock
    $stocks = $inwards->map(function ($item) use ($outwards) {
        $key = "{$item->batch_number}-{$item->services}-{$item->size}-{$item->stage}";
        $out_qty = $outwards[$key]->outward_qty ?? 0;

        return [
            'batch_number' => $item->batch_number,
            'services'     => $item->services,
            'service_name' => $item->service_name,
            'size'         => $item->size,
            'size_name'    => $item->size_name,
            'stage'        => $item->stage,
            'inward_qty'   => $item->inward_qty,
            'stock_qty'    => max($item->inward_qty - $out_qty, 0),
        ];
    });

    return response()->json([
        'status' => 'success',
        'data'   => $stocks
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

    public function edit(Farm_Delivery_challan $Farm_Delivery_challan)
    {


        $invoice = DB::table('farm_delivery_challan')->where('id',  $Farm_Delivery_challan->id)->first();

        $Farm_Delivery_challan = Farm_Delivery_challan::where('id', $Farm_Delivery_challan->id)->first();

        $Farm_Delivery_challan_details = Farm_Delivery_challan_details::where('pid', $Farm_Delivery_challan->id)->get();

        return view('admin.Farm_Delivery_challan.edit', ['Farm_Delivery_challan' => $Farm_Delivery_challan],compact('Farm_Delivery_challan','Farm_Delivery_challan_details','invoice'));

    }

    /**
     * Update the specified inward record and its details.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Farm_Delivery_challan $Farm_Delivery_challan)
    {
        return $this->repository->update($request->all(), $Farm_Delivery_challan->id);
    }

    /**
     * Remove the specified inward record and its details from storage.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Farm_Delivery_challan $Farm_Delivery_challan)
    {
        return $this->repository->destroy($Farm_Delivery_challan->id);
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
