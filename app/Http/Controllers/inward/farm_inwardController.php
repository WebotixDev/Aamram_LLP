<?php

namespace App\Http\Controllers\inward;

use App\Models\farm_inward;
use App\Models\farm_inward_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\farm_inwardDataTale;
use App\Http\Controllers\Controller;
use App\Repositories\farm_inwardRepository;
use Illuminate\Support\Facades\DB;

class farm_inwardController extends Controller
{
    protected $repository;

    public function __construct(farm_inwardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the inward records.
     *
     * @param farm_inwardDataTale $dataTable
     * @return \Illuminate\View\View
     */
    public function index(farm_inwardDataTale $dataTable)
    {
        return $dataTable->render('admin.farm_inward.index');
    }

    /**
     * Show the form for creating a new inward record.
     *
     * @return \Illuminate\View\View
     */
    public function create(farm_inward $farm_inward)
    {

        return view('admin.farm_inward.create', ['farm_inward' => $farm_inward]);

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

public function farmgetRates(Request $request)
{
    $services_id = $request->services;
    $size_id = $request->size;

    $rate = DB::table('product_details')
            ->where('parentID',$services_id)
            ->where('id',$size_id)
            ->value('purch_price');

    return response()->json([
        'status' => 'success',
        'rate' => $rate ?? 0
    ]);
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

public function getInvoiceBatch(Request $request)
{
    $invoice = \App\Helpers\Helpers::getNextInvoiceForFarmInward($request->location_id);
    $batch =  \App\Helpers\Helpers::getNextBatchForFarmInward($request->location_id);

    return response()->json([
        'invoice' => $invoice,
        'batch' => $batch
    ]);
}
    /**
     * Display the specified inward record along with its details.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $farm_inward = farm_inward::with('details')->findOrFail($id);
        return view('admin.farm_inward.show', compact('farm_inward'));
    }


    /**
     * Show the form for editing the inward record.
     *
     * @param farm_inward $inward
     * @return \Illuminate\View\View
     */

    public function edit(farm_inward $farm_inward)
    {


        $invoice = DB::table('farm_inward')->where('id',  $farm_inward->id)->first();

        $purchase_details = farm_inward::where('id', $farm_inward->id)->first();

        $purchase_product_list = farm_inward_details::where('pid', $farm_inward->id)->get();

        return view('admin.farm_inward.edit', ['farm_inward' => $farm_inward],compact('purchase_details','purchase_product_list','invoice'));

    }

    /**
     * Update the specified inward record and its details.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, farm_inward $farm_inward)
    {
        return $this->repository->update($request->all(), $farm_inward->id);
    }

    /**
     * Remove the specified inward record and its details from storage.
     *
     * @param Request $request
     * @param farm_inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, farm_inward $farm_inward)
    {
        return $this->repository->destroy($farm_inward->id);
    }


    public function FarminwardBill(Request $request)
{
    $id = $request->query('id'); // or $request->get('id')
        // Fetch sale order, sale order details, and customer details using joins
        $saleOrder = DB::table('farm_inward')
            ->where('farm_inward.id', $id)
            ->leftJoin('farm_inward_details', 'farm_inward.id', '=', 'farm_inward_details.pid')
            ->leftJoin('supplier', 'farm_inward.supplier_id', '=', 'supplier.id') // Join with the customers table
            ->select(
                'farm_inward_details.*',
                'farm_inward.*',
                'supplier.supplier_name',
                'supplier.mobile_no',
                'supplier.address',
            )
            ->first();  // Use first() if you expect only one sale order for the given ID

        // Fetch the sale order details separately
        $saleOrderDetails = DB::table('farm_inward_details')
        ->where('farm_inward_details.pid', $saleOrder->id) // Assuming pid is the sale order ID
        ->leftJoin('products', 'farm_inward_details.services', '=', 'products.id') // Joining products
        ->select('farm_inward_details.*', 'products.product_name') // Selecting necessary fields
        ->get();


        return view('admin.farm_inward.farmbill', compact('saleOrder', 'saleOrderDetails'));
    }
}
