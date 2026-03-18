<?php

namespace App\Http\Controllers\inward;

use App\Models\inward;
use App\Models\inward_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\InwardDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\InwardRepository;
use Illuminate\Support\Facades\DB;

class InwardController extends Controller
{
    protected $repository;

    public function __construct(InwardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the inward records.
     *
     * @param InwardDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(InwardDataTable $dataTable)
    {
        return $dataTable->render('admin.inward.index');
    }

    /**
     * Show the form for creating a new inward record.
     *
     * @return \Illuminate\View\View
     */
    public function create(inward $inward)
    {
        
        $sizes = $this->getSizes(); // A collection of Size models
      
        return view('admin.inward.create', ['purchase_details' => $inward],compact('sizes'));
        
       
    }
    public function getproducts()
    {
        return \App\Models\Product::all()->pluck('id', 'product_name');
    }
 
    public function getSizes()
    {
        return \App\Models\Product_details::all()->pluck('product_size', 'id');
    }


    public function getprice(Request $request) {
        $services_id = $request->input('services');

        // Fetch the product
        $product = Product::find($services_id);
        
        // Fetch the related product details
        $productdetails = Product_details::where('parentID', $product->id)->get(['id', 'product_size']);
    
        // Return the result with price
        return response()->json([
            'status' => 'success',
            'data' => $productdetails,
            
        ]);
    }


    public function getstock(Request $request) {
       $servicesId = $request->input('services');
      
       $Date = date('Y-m-d');
       $stock = \App\Helpers\Helpers::getstockpeti($servicesId, $Date);

        return response()->json([
            'status' => 'success',
            'stock' => $stock,  // Return the product detail
           // 'stock' => $stock,
        ]);
    }
    

    public function getProductSizes(Request $request)
    {    

     
        $servicesId = $request->input('services'); // Get the selected service ID
        $productsizes = $request->input('productsizes'); // Get the selected product size
        $rowindex = $request->input('rowindex'); // Get the row index (although you only have one row)
    
        // Fetch the product
          $product = DB::table('products')->find($servicesId);

        // Fetch the related product details for the selected size (assuming only one result)
        $productdetail = DB::table('product_details')->where('parentID', $product->id)
            ->where('product_size', 'like', '%' . $productsizes . '%')
            ->first(['id', 'purch_price']);  // Use `first()` since only one row is expected
        
        // Get the stock information
       // $Date = date('Y-m-d');
       // $stock = \App\Helpers\Helpers::getstockpeti($servicesId, $Date);
    
        // Check if the product detail was found
        if (!$productdetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product detail not found for the selected size.'
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'productdetail' => $productdetail,  // Return the product detail
           // 'stock' => $stock,
        ]);
    }
    


    
    public function getRates(Request $request)
{
    $services_id = $request->input('services');
    $size_id = $request->input('size');

    // Fetch the rate from the product_details table
    $rate = DB::table('product_details')
        ->where('parentID', $services_id)
        ->where('id',$size_id)
        ->value('purch_price');

    // Return the result as a JSON response
    return response()->json([
        'status' => 'success',
        'rate' => $rate,
    
    ]);
}
    public function getData(InwardDataTable $dataTable)
    {
        return $dataTable->ajax();
    }
    
   public function getProductsByType($type)
{

    $product = DB::table('products')->where('type', $type)->get();

    return response()->json($product);
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

    public function show($id)
    {
        $inward = inward::with('details')->findOrFail($id);
        return view('admin.inward.show', compact('inward'));
    }

    /**
     * Show the form for editing the inward record.
     *
     * @param Inward $inward
     * @return \Illuminate\View\View
     */
  
    public function edit(inward $inward)
    {

        $purchase_details = inward::first();
        $purchase_product_list = inward_details::where('pid', $inward->id)->get();
        $sizes = $this->getSizes(); // A collection of Size models
        $invoice = DB::table('purchase_details')->where('id',  $inward->id)->first();
      
        return view('admin.inward.edit', ['purchase_details' => $inward],compact('purchase_details','sizes','purchase_product_list','invoice'));

    }

    /**
     * Update the specified inward record and its details.
     *
     * @param Request $request
     * @param Inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, inward $inward)
    {
   
        return $this->repository->update($request->all(), $inward->id);
    }
 
    /**
     * Remove the specified inward record and its details from storage.
     *
     * @param Request $request
     * @param Inward $inward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, inward $inward)
    {
        return $this->repository->destroy($inward->id);
    }
}
