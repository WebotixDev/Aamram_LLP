<?php

namespace App\Http\Controllers\outward;


use App\Models\outward_details;
use App\Models\Product;
use Illuminate\Http\Request;
use App\DataTables\OutwardDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\OutwardRepository;
use Illuminate\Support\Facades\DB;

class OutwardController extends Controller
{
    protected $repository;

    public function __construct(OutwardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the outward records.
     *
     * @param OutwardDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(OutwardDataTable $dataTable)
    {
        return $dataTable->render('admin.outward.index');
    }

    /**
     * Show the form for creating a new outward record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.outward.create', [
            'products' => $this->getProducts(),
        ]);
    }

    public function getProducts()
    {
        return Product::all()->pluck('id', 'product_name');
    }

//    public function getOrders(Request $request)
//     {
//             $season = session('selected_season');

//             $customerID = $request->input('customerID');

//             // Fetch orders based on the customer ID
//             $orders = DB::table('sale_orderdetails')
//                         ->where('season', $season)
//                         ->where('customer_name', $customerID)
//                         ->get();

//             return response()->json(['orders' => $orders]);

//     }

        public function getOrders(Request $request)
        {
            $season = session('selected_season');
            $customerID = $request->input('customerID');

            $orders = DB::table('sale_orderdetails')
                ->where('season', $season)
                ->where('customer_name', $customerID)
                ->get();

            $filteredOrders = [];

            foreach ($orders as $order) {

                $items = DB::table('sale_order')
                    ->where('pid', $order->id)
                    ->get();

                $hasRemaining = false;

                foreach ($items as $item) {

                    $dispatch = DB::table('outward_details')
                        ->where('order_no', $order->id)
                        ->where('services', $item->services)
                        ->where('size', $item->size)
                        ->where('stage', $item->stage)
                        ->where('customer_name', $customerID)
                        ->sum('currdispatch_qty');

                    $rem = $item->qty - $dispatch;

                    if ($rem > 0) {
                        $hasRemaining = true;
                        break;
                    }
                }

                if ($hasRemaining) {
                    $filteredOrders[] = $order;
                }
            }

            return response()->json(['orders' => $filteredOrders]);
        }
    public function getOrderRecords(Request $request)
    {
        // Get the orderID (pid) from the request
        $pid = $request->input('orderID');

        if (!$pid) {
            return response()->json(['error' => 'orderID is required'], 400);
        }

        $query = DB::table('sale_order')
            ->leftJoin('products', 'sale_order.services', '=', 'products.id') // Join with products table on services field
            ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id')
            ->where('sale_order.pid', $pid)
            ->select(
                'sale_order.services',
                'product_details.product_size as p_size',  // Select size_name from product_details table
                'sale_order.stage',
                'sale_order.Quantity',
                'sale_order.qty',
                'sale_order.size',
                'products.product_name as service_name', // Select product_name from products table

            )
            ->get(); // Execute the query and get the result as a collection



        $data = [];

        // Iterate over the query results and format them
        foreach ($query as $info) {


            $dispatch1 =  DB::table('sale_orderdetails')
            ->where('id', $pid)
            ->value('customer_name');

            $dispatch = DB::table('outward_details')
                 ->where('order_no', $pid)
                  ->where('services', $info->services)
                 ->where('size', $info->size)
                 ->where('stage', $info->stage)
                 ->where('customer_name', $dispatch1)
                ->sum('currdispatch_qty');




            // $remain = DB::table('outward_details')
            // ->where('Invoicenumber', $dispatch )
            // ->sum('rem_qty');
            $finaldispatch = $dispatch;
            $rem = $info->qty - $finaldispatch;

    if ($rem <= 0) {
        continue;
    }
            //$amt=  ($info->qty -$info->Quantity) - $dispatch ;

            $data[] = [
                'servicesid' => $info->services ?? null,
                'services' => $info->service_name ?? null,
                'size' => $info->size ?? null,
                'p_size' => $info->p_size ?? null,
                'stage' => $info->stage ?? null,
                'Quantity' => $finaldispatch ?? 0,
                'qty' => $info->qty ?? 0,

                'rem_qty' =>  $rem,
            ];

        }
        return response()->json(['data' => $data], 200);
    }

    public function QRBulklPrint(Request $request)
    {

        // Retrieve selected row IDs from request
        $selectedRows = explode(',', $request->input('selected_rows', ''));

        // If no rows are selected, return with an error
        if (empty($selectedRows) || count($selectedRows) == 0) {
            return redirect()->back()->with('error', 'Please select at least one record.');
        }

        // Fetch outward details for selected rows
        $saleOrderss = DB::table('outward_details')
            ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
            ->leftJoin('states', 'customers.state_id', '=', 'states.id')
             ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id') // Join with sale_orderdetails
            ->whereIn('outward_details.id', $selectedRows)
            ->select(
                'outward_details.id',
                'outward_details.order_no',
                'outward_details.billdate',
                'customers.customer_name',
                'customers.mobile_no',
                'customers.pin_code',
                'customers.city_name',
                'customers.district_id',
                'customers.address',
                'sale_orderdetails.order_address',
                'states.name as state_name'
            )
            ->get();

        return view('admin.outward.BulkQR', compact('saleOrderss'));
    }



  public function QRBulklargePrint(Request $request)
    {
        // Retrieve selected row IDs from request
        $selectedRows = explode(',', $request->input('selected_rows', ''));

        // If no rows are selected, return with an error
        if (empty($selectedRows) || count($selectedRows) == 0) {
            return redirect()->back()->with('error', 'Please select at least one record.');
        }

        // Fetch outward details for selected rows
        $saleOrderss = DB::table('outward_details')
            ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
            ->leftJoin('states', 'customers.state_id', '=', 'states.id')
             ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id') // Join with sale_orderdetails
            ->whereIn('outward_details.id', $selectedRows)
            ->select(
                'outward_details.id',
                'outward_details.order_no',
                'outward_details.billdate',
                'customers.customer_name',
                'customers.mobile_no',
                 'customers.pin_code',
                'customers.city_name',
                'customers.district_id',
                'customers.address',
                 'sale_orderdetails.order_address',
                'states.name as state_name'
            )
            ->get();

        return view('admin.outward.BulkQR_largee', compact('saleOrderss'));
    }




    // public function generateQR($id)
    // {
    //     $saleOrderss = DB::table('outward_details')
    // ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
    // ->leftJoin('states', 'customers.state_id', '=', 'states.id')
    // ->where('outward_details.id', $id) // Filter by outward detail ID
    // ->select(
    //     'outward_details.billdate',
    //     'customers.customer_name',
    //     'customers.mobile_no',
    //     'customers.city_name',
    //     'customers.district_id',

    //     'customers.address',
    //     'states.name as state_name',
    // )
    // ->first(); // Get single result


    //     // Fetch outward details
    //     $outwardDetails = DB::table('outward_details')
    //         ->where('id', $id)
    //         ->select('order_no', 'services', 'size') // Fetch order_no and size ID
    //         ->first();

    //     // Fetch batch history with product names
    //     $batchHistory = DB::table('batch_history')
    //         ->join('products', 'batch_history.productid', '=', 'products.id')
    //         ->where('batch_history.orderid', $outwardDetails->order_no)
    //         ->select('batch_history.*', 'products.product_name')
    //         ->get();

    //     return view('admin.outward.show-qr', compact('id', 'batchHistory', 'saleOrderss'));
    // }

 public function generateQR($id)
    {
        $saleOrderss = DB::table('outward_details')
    ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
    ->leftJoin('states', 'customers.state_id', '=', 'states.id')
      ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id') // Join with sale_orderdetails

    ->where('outward_details.id', $id) // Filter by outward detail ID
    ->select(
        'outward_details.billdate',
        'customers.customer_name',
        'customers.mobile_no',
        'customers.city_name',
        'customers.district_id',
          'customers.pin_code',
        'sale_orderdetails.order_address',
        'customers.address',
        'states.name as state_name',
    )
    ->first(); // Get single result


        // Fetch outward details
        $outwardDetails = DB::table('outward_details')
            ->where('id', $id)
            ->select('order_no', 'services', 'size') // Fetch order_no and size ID
            ->get();



        return view('admin.outward.show-qr', compact('id', 'outwardDetails', 'saleOrderss'));
    }


    // public function BulklabourBill(Request $request)
    // {
    //     $selectedRows = explode(',', $request->input('selected_rows', ''));

    //     // If no rows are selected, return with an error
    //     if (empty($selectedRows) || count($selectedRows) == 0) {
    //         return redirect()->back()->with('error', 'Please select at least one record.');
    //     }

    //     // Fetch all required data in a single query
    //     $saleOrders = DB::table('outward_details')
    //         ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
    //         ->leftJoin('states', 'customers.state_id', '=', 'states.id')
    //         ->leftJoin('products', 'outward_details.services', '=', 'products.id')
    //         ->leftJoin('product_details', 'outward_details.size', '=', 'product_details.id')
    //         ->whereIn('outward_details.id', $selectedRows)
    //         ->select(
    //             'outward_details.id',
    //             'outward_details.order_no',
    //             'outward_details.billdate',
    //             'customers.customer_name',
    //             'customers.mobile_no',
    //             'customers.city_name',
    //             'customers.district_id',
    //             'customers.address',
    //             'states.name as state_name',
    //             'products.product_name',
    //             'product_details.product_size',
    //             'outward_details.currdispatch_qty'
    //         )
    //         ->get();

    //     return view('admin.outward.LabourBulk', compact('saleOrders'));
    // }

    public function Farm_bulkReceiptBill(Request $request)
    {
                  $from_date = $request->input('from_date');
    $to_date   = $request->input('to_date');
        $selectedRows = explode(',', $request->input('selected_rows', ''));

        // If no rows are selected, return with an error
        if (empty($selectedRows) || count($selectedRows) == 0) {
            return redirect()->back()->with('error', 'Please select at least one record.');
        }

        // Fetch all required data in a single query
        $saleOrders = DB::table('outward_details')
            ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
            ->leftJoin('states', 'customers.state_id', '=', 'states.id')
            ->leftJoin('products', 'outward_details.services', '=', 'products.id')
            ->leftJoin('product_details', 'outward_details.size', '=', 'product_details.id')
            ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id')
            ->whereIn('outward_details.id', $selectedRows)
            ->select(
                'outward_details.id',
                'outward_details.*',
                'outward_details.order_no',
                'outward_details.billdate',
                'outward_details.services',
                'customers.customer_name',
                'customers.mobile_no',
                'customers.city_name',
                'customers.district_id',
                'customers.address',
                 'sale_orderdetails.order_address',
                'states.name as state_name',
                'products.product_name',
                'product_details.product_size',
                'outward_details.currdispatch_qty'
            )
            ->orderBy('outward_details.services') // Ensuring grouped order
            ->get()
            ->groupBy('services'); // Grouping by services

        return view('admin.outward.Farm_BulkReciept', compact('saleOrders','from_date','to_date'));
    }

    /**
     * Store a newly created outward record and its details.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified outward record along with its details.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $orderId = $request->get('orderid');
        $product = $request->get('product');
        $size = $request->get('size');

        // Check if any parameter is missing
        if (!$orderId || !$product ||  !$size) {
            abort(404, 'Invalid QR Code Data');
        }

        return view('info.index', compact('orderId', 'product',  'size'));
    }

    /**
     * Show the form for editing the outward record.
     *
     * @return \Illuminate\View\View
     */
    public function edit(outward_details $outward)
    {
        // Get the first record (this can be removed if not needed)
        $outwarddet = outward_details::first();

        // Get the outward detail with the given ID
        $outwardsdetails = outward_details::where('id', $outward->id)->first();

        // Return the view with the appropriate data
        return view('admin.outward.edit', [
            'outwarddet' => $outward,
            'outwardsdetails' => $outwardsdetails
        ]);
    }



    /**
     * Update the specified outward record and its details.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, outward_details $outward)
    {
        return $this->repository->update($request->all(), $outward->id);
    }

    /**
     * Remove the specified outward record and its details from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, outward_details $outward)
    {
        return $this->repository->destroy($outward->id);
    }
}
