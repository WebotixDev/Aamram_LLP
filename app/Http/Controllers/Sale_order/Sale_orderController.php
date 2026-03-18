<?php

namespace App\Http\Controllers\sale_order;

use App\Models\sale_order;
use App\Models\sale_details;
use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\Sale_orderDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Sale_orderRepository;
use app\Helpers\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Razorpay\Api\Api;
// use Razorpay\Api\Errors\SignatureVerificationError;
// use Razorpay\Api\Utility;
use Illuminate\Support\Facades\Response;



class Sale_orderController extends Controller
{
    protected $repository;

    public function __construct(Sale_orderRepository $repository)
    {
        $this->repository = $repository;

    }

    /**
     * Display a listing of the sale orders.
     *
     * @param Sale_orderDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Sale_orderDataTable $dataTable)
    {
        return $dataTable->render('admin.sale_order.index');
    }

    /**
     * Show the form for creating a new sale order.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.sale_order.create', [
            'customers' => $this->getCustomers(),
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'districts' => $this->getDistricts(),
        ]);
    }

    /**
     * Retrieve all customers for the dropdown.
     *
     * @return \Illuminate\Support\Collection
     */


     public function getCountries()
     {
         return \App\Models\Country::all()->pluck('name', 'id');
     }

    public function getStates()
    {
        return \App\Models\State::all()->pluck('name', 'id');
    }

    /**
     * Fetch all districts.
     * @return Collection
     */
    public function getDistricts()
    {
        return \App\Models\District::all()->pluck('district_name', 'id');
    }

    public function getCitiesByDistrict(Request $request)
    {
        $districtId = $request->district_id;
        $cities = \App\Models\City::where('district_id', $districtId)->pluck('name', 'id');

        return response()->json($cities);
    }


    public function getDistrictsByState(Request $request)
    {
        if ($request->has('state_id')) {
            $districts = \App\Models\District::where('state_id', $request->state_id)->get(['district_name', 'id']);
            return response()->json(['districts' => $districts]);
        }
        return response()->json(['districts' => []], 400);
    }

    public function getCustomers()
    {
        return \App\Models\Customer::all()->pluck('id', 'customer_name');
    }

    public function customersstore(Request $request)
{

    // Directly insert data into the database without validation
    $customer = DB::table('customers')->insertGetId([
        'customer_name' => $request->input('customer_name'),
        'mobile_no' => $request->input('customer_number'),
        'vendor' => $request->input('customer_type'),
        'state_id' => $request->input('state_id'),
        'district_id' => $request->input('district_id'),
        'city_name' => $request->input('city_name'),
        'address' => $request->input('address'),
        'wp_number' => $request->input('wp_number'),
        'pin_code' => $request->input('pin_code'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $customer,
            'customer_name' => $request->input('customer_name'),
        ],
    ]);
}


// public function getPrice(Request $request) {
//     $services_id = $request->input('services');
//     $rowIndex = $request->input('rowIndex');
//     $gst = $request->input('gst');
//     // Fetch the product
//     $product = Product::find($services_id);

//     // Fetch the related products
//     $productdetails = Product_details::where('parentID', $product->id)->where('sizeoff', 0)->get(['id', 'product_size']);
//     $productprice = Product_details::where('parentID', $product->id)->where('sizeoff', 0)->first(['id', 'dist_price']); // Fetch the price for the first item

//     $total_gst = ($gst == 'Maharashtra') ? ($product->cgst + $product->sgst) : $product->igst;

//     // Return the result
//     return response()->json([
//         'status' => 'success',
//         'data' => $productdetails,
//         'price' => $productprice->dist_price,
//         'gst'=> $total_gst,
//         'pro_data' => [
//             'cgst' => $product->cgst ?? 0,
//             'sgst' => $product->sgst ?? 0,
//             'igst' => $product->igst ?? 0,
//         ],    // Return the price

//     ]);
// }

public function getData(Sale_orderDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

public function getStock(Request $request)
{
    $services_id = $request->input('services');
    $size = $request->input('size');
    $stage = $request->input('stage');


    // Fetch the first product from the purchase_product table
    // $product = DB::table('purchase_product')
    //     ->where('services', $services_id)
    //     ->where('complete_flag', 0)
    //     ->orderBy('created_at', 'asc')
    //     ->first();

    // // Fetch batch_id from the purchase_details table for the found product
    // $batchIds = [];
    // if ($product) {
    //     $batchIds = DB::table('purchase_details')
    //         ->where('id', $product->pid)
    //         ->where('complete_flag', 0)
    //         ->pluck('batch_id')
    //         ->toArray();
    // }

    $Date = date('Y-m-d');

    // Pass only values of batch IDs to the helper function
    $stock = \App\Helpers\Helpers::getstockbatch($services_id,$size,$stage,$Date);

    // Return the result as a JSON response
    return response()->json([
        'status' => 'success',
        'stock' => $stock,
    ]);
}

// public function getRate(Request $request)
// {
//     $services_id = $request->input('services');
//     $size_id = $request->input('size');

//     // Fetch the rate from the product_details table
//     $rate = DB::table('product_details')
//         ->where('parentID', $services_id)
//         ->where('id',$size_id)
//         ->value('dist_price');

//     // Return the result as a JSON response
//     return response()->json([
//         'status' => 'success',
//         'rate' => $rate,

//     ]);
// }


public function getRate(Request $request)
{
    $services_id = $request->input('services');
    $size_id = $request->input('size');

    // Fetch the rate from the product_details table
    $rate = DB::table('product_details')
        ->where('parentID', $services_id)
        ->where('id',$size_id)
        ->value('dist_price');

    // Return the result as a JSON response
    return response()->json([
        'status' => 'success',
        'rate' => $rate,

    ]);
}

public function getPrice(Request $request)
{
    $services_id = $request->services;
    $gst = $request->gst;

    // Find product
    $product = Product::find($services_id);

    if(!$product){
        return response()->json([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
    }

    // Get sizes
    $sizes = Product_details::where('parentID',$product->id)
                ->where('sizeoff',0)
                  ->where('status',1)
                ->where('disable','!=',1)
                ->get(['id','product_size']);

    // ❗ If product has no size
    if($sizes->isEmpty()){
        return response()->json([
            'status' => 'nosize',
            'data' => []
        ]);
    }

    // First price
    $productprice = Product_details::where('parentID',$product->id)
                        ->where('sizeoff',0)
                        ->where('status',1)
                        ->where('disable','!=',1)
                        ->first();

    // GST calculation
    $total_gst = ($gst == 'Maharashtra')
                    ? ($product->cgst + $product->sgst)
                    : $product->igst;

    return response()->json([
        'status' => 'success',
        'data' => $sizes,
        'price' => $productprice->dist_price ?? 0,
        'gst' => $total_gst,
        'pro_data' => [
            'cgst' => $product->cgst ?? 0,
            'sgst' => $product->sgst ?? 0,
            'igst' => $product->igst ?? 0,
        ]
    ]);
}
    /**
     * Store a newly created sale order and its details.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified sale order along with its details.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    // public function show($id)
    // {
    //     $salerazorpay = sale_details::with('details')->findOrFail($id);
    //     return view('razorpay.index', compact('salerazorpay'));
    // }

      public function show(Request $request)
        {
             $id = $request->query('id'); // or $request->get('id')

            $salerazorpay = sale_details::with('details')->findOrFail($id);
            return view('razorpay.index', compact('salerazorpay'));
        }

    public function paymentRazorpay(Request $request)
    {

        // Capture the data from the AJAX request
        $paymentId = $request->input('payment_id');
        $orderId = $request->input('order_id');
        $signature = $request->input('signature');
        $amount = $request->input('amount');
        $customerName = $request->input('customer_name');
        $sale_id = $request->input('sale_id');


       // Insert the payment data into the razorpay_history table
    //     $insertedId = DB::table('razorpay_history')->insertGetId([
    //         'transaction_id' => $paymentId,
    //         'order_id' => $sale_id,
    //         'signature' => $signature,
    //         'paid_amount' => $amount,
    //         'customer_name' => $customerName,
    //         'created_at' => now(),
    //     ]);

    //     $count = DB::table('purchase_payments')->count();

    //     // Determine the next Bill_No based on existing records
    //     $nextBillNo = $count > 0 ? $count + 1 : 1;
    //      // Directly insert data into the database without validation
    //   $insertPayment = DB::table('purchase_payments')->insertGetId([
    //     'user_id'  =>'razorpay',
    //     'ReceiptNo'   =>$nextBillNo ,
    //     // 'user_id'        => Auth::id(),
    //     'PurchaseDate'   => now()->format('Y-m-d'),
    //         'customer_name'  => $customerName,
    //         'amt_pay'        => $amount,
    //         'totalvalue'     => $amount,
    //         // 'sale_id'        => $sale_id,
    //         'mode' => 'online',
    //         'complete_flag'  =>'online',
    //         'created_at'     => now()->format('Y-m-d'),
    // ]);


    //     $insertPaymentInfo = [ // Use '=' instead of '['
    //         'pid'           => $insertPayment, // assuming $salePaymentId is available
    //         'Invoicenumber' => $sale_id,
    //         'amount'        => $amount,
    //         'created_at'    => now()->format('Y-m-d'),
    //         'updated_at'    => now()->format('Y-m-d'),
    //     ];

    //   DB::table('purchase_payment_info')->insert($insertPaymentInfo);



        // Optionally, return the inserted ID or a success response
        return response()->json([
            'success' => true,

        ]);
    }




public function handleWebhook(Request $request)
{
    $secret = 'aamram@123';
    $payload = $request->getContent();
    $signature = $request->header('X-Razorpay-Signature');
    $expectedSignature = hash_hmac('sha256', $payload, $secret);

    // Log the initial data
    Log::info('Webhook Received', [
        'signature' => $signature,
        'expected_signature' => $expectedSignature,
        'payload' => $payload,
    ]);

    // Validate signature
    if (!hash_equals($expectedSignature, $signature)) {
        Log::error('Signature mismatch', [
            'expected' => $expectedSignature,
            'received' => $signature,
        ]);
        return Response::json(['status' => 'signature mismatch'], 400);
    }

    // Decode the payload
    $data = json_decode($payload, true);
    if (!$data || !isset($data['event'], $data['payload']['payment']['entity'])) {
        Log::error('Invalid webhook payload', ['data' => $data]);
        return Response::json(['status' => 'invalid payload'], 400);
    }

    $event = $data['event'];
    $payment = $data['payload']['payment']['entity'];

    // Log the valid event
    Log::info('Webhook verified', [
        'event' => $event,
        'payment_data' => $payment,
    ]);

    // Process only 'payment.captured'
    if ($event === 'payment.captured') {
        DB::table('user_order_payment_transtion')->insert([
            'order_id'         => $payment['order_id'] ?? null,
            'payment_id'       => $payment['id'] ?? null,
            'payment_order_id' => $payment['order_id'] ?? null,
            'amount'           => $payment['amount'] ?? null,
            'currency'         => $payment['currency'] ?? null,
            'email'            => $payment['email'] ?? null,
            'contact'          => $payment['contact'] ?? null,
            'event'            => $event,
            'status'           => $payment['status'] ?? null,
        ]);

        Log::info('Payment data inserted into DB');
    }

    return Response::json(['status' => 'success'], 200);
}


    /**
     * Show the form for editing the sale order.
     *
     * @param sale_order $saleOrder
     * @return \Illuminate\View\View
     */

    // public function edit( $saleOrder)
    // {

    //     $saleOrders = sale_details::where('id',$saleOrder)->first();
    //     $sale_order_list = sale_order::where('pid',$saleOrder)->get();

    //     return view('admin.sale_order.edit', ['saleOrders' => $saleOrders,
    //      'countries' => $this->getCountries(),
    //         'states' => $this->getStates(),
    //         'districts' => $this->getDistricts(),],compact('saleOrders','sale_order_list'));

    // }


  public function edit( $sale_order)
    {

     $saleOrder = sale_order::where('id', $sale_order)->pluck('pid');

        $saleOrders = sale_details::where('id',$saleOrder)->first();
        $sale_order_list = sale_order::where('pid',$saleOrder)->get();

        return view('admin.sale_order.edit', ['saleOrders' => $saleOrders,
         'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'districts' => $this->getDistricts(),],compact('saleOrders','sale_order_list'));

    }

    /**
     * Update the specified sale order and its details.
     *
     * @param Request $request
     * @param sale_order $saleOrder
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request,  $saleOrder)
    {
        return $this->repository->update($request->all(), $saleOrder);
    }
public function saleBill(Request $request)
{
    $id = $request->query('id'); // or $request->get('id')
        // Fetch sale order, sale order details, and customer details using joins
        $saleOrder = DB::table('sale_orderdetails')
            ->where('sale_orderdetails.id', $id)
            ->leftJoin('sale_order', 'sale_orderdetails.id', '=', 'sale_order.pid')
            ->leftJoin('customers', 'sale_orderdetails.customer_name', '=', 'customers.id') // Join with the customers table
            ->leftJoin('states', 'customers.state_id', '=', 'states.id') // Join with states table
            ->select(
                'sale_order.*',
                'sale_orderdetails.*',
                'customers.customer_name',
                'customers.mobile_no',
                'customers.pin_code',
                'customers.address',
                 'customers.state_id',
                'customers.district_id',
                'customers.city_name',
                'states.name as state_name'
            )
            ->first();  // Use first() if you expect only one sale order for the given ID

        // Fetch the sale order details separately
        $saleOrderDetails = DB::table('sale_order')
        ->where('sale_order.pid', $saleOrder->pid) // Assuming pid is the sale order ID
        ->leftJoin('products', 'sale_order.services', '=', 'products.id') // Joining products
        ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id') // Joining product_details based on size_id
        ->select('sale_order.*', 'products.product_name', 'product_details.product_size') // Selecting necessary fields
        ->get();


        return view('admin.sale_order.bill', compact('saleOrder', 'saleOrderDetails'));
    }

    public function labourBill($id)
    {
        // Fetch sale order, sale order details, and customer details using joins
        $saleOrder = DB::table('sale_orderdetails')
            ->where('sale_orderdetails.id', $id)
            ->leftJoin('sale_order', 'sale_orderdetails.id', '=', 'sale_order.pid')
            ->leftJoin('customers', 'sale_orderdetails.customer_name', '=', 'customers.id') // Join with the customers table
            ->leftJoin('states', 'customers.state_id', '=', 'states.id') // Join with states table
            ->select(
                'sale_order.*',
                'sale_orderdetails.*',
                'customers.customer_name',
                'customers.mobile_no',
                'customers.address',

                'customers.state_id',
                'customers.district_id',
                'customers.city_name',
                'states.name as state_name',
            )
            ->first();  // Use first() if you expect only one sale order for the given ID

        // Fetch the sale order details separately
        $saleOrderDetails = DB::table('sale_order')
        ->where('sale_order.pid', $saleOrder->pid) // Assuming pid is the sale order ID
        ->leftJoin('products', 'sale_order.services', '=', 'products.id') // Joining products
        ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id') // Joining product_details based on size_id
        ->select('sale_order.*', 'products.product_name', 'product_details.product_size') // Selecting necessary fields
        ->get();


        // Pass the data to the view
        return view('admin.sale_order.labour', compact('saleOrder', 'saleOrderDetails'));
    }






public function BulklabourBillPrint(Request $request)
{
    // Retrieve date range from request parameters
    $from_date = $request->input('from_date');
    $to_date   = $request->input('to_date');

    // Query sale orders with related information.
    // This join might return multiple rows for one sale order if there are multiple details.
    $saleOrders = DB::table('sale_orderdetails')
        ->leftJoin('sale_order', 'sale_orderdetails.id', '=', 'sale_order.pid')
        ->leftJoin('customers', 'sale_orderdetails.customer_name', '=', 'customers.id')
        ->leftJoin('states', 'customers.state_id', '=', 'states.id')
        ->select(
            'sale_order.pid',
           'sale_orderdetails.*',

            'sale_orderdetails.billdate',
            'customers.customer_name',
            'customers.mobile_no',
            'customers.city_name',
            'customers.district_id',
            'customers.address',

            'states.name as state_name'
        )
        ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
            return $query->whereBetween('sale_orderdetails.billdate', [$from_date, $to_date]);
        })
        ->get();

    // Remove duplicate sale orders based on pid
    $uniqueSaleOrders = $saleOrders->unique('pid');

    // Extract all pids from the unique sale orders
    $pids = $uniqueSaleOrders->pluck('pid')->unique();

    // Query details for all sale orders at once
    $details = DB::table('sale_order')
        ->leftJoin('products', 'sale_order.services', '=', 'products.id')
        ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id')
        ->select(
            'sale_order.pid',
            'products.product_name',
            'product_details.product_size',
            'sale_order.qty'
        )
        ->whereIn('sale_order.pid', $pids)
        ->get();

    // Group the details by sale order pid
    $groupedDetails = $details->groupBy('pid');

    // Pass the unique sale orders and grouped details to the view
    return view('admin.sale_order.Bulk_labour_bill', compact('uniqueSaleOrders', 'groupedDetails', 'from_date', 'to_date'));
}

    public function paymentSuccess(Request $request)
    {

        try {
            // Insert payment details directly into the razorpay_history table
            DB::table('razorpay_history')->insert([
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'transaction_id' => $request->amount,
                'created_at' => now(),
            ]);

            return response()->json(['message' => 'Payment recorded successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['message' => 'Payment recording failed'], 500);
        }
    }

    /**
     * Remove the specified sale order and its details from storage.
     *
     * @param Request $request
     * @param sale_order $saleOrder
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function destroy(Request $request, $saleOrder)
    // {
    //     return $this->repository->destroy($saleOrder);
    // }

 public function destroy(Request $request, $sale_order)
    {

     $saleOrder = DB::table('sale_order')->where('id', $sale_order)->value('pid');
         return $this->repository->destroy($saleOrder);
    }

      public function getCustomerAddresses($id)
    {

        $customer = DB::table('customers')->where('id', $id)->first();

        $addresses = [];

        if ($customer) {
            if (!empty($customer->address)) {
                $addresses[] = ['value' => $customer->address, 'label' => $customer->address];
            }
            if (!empty($customer->address1)) {
                $addresses[] = ['value' => $customer->address1, 'label' => $customer->address1];
            }
            if (!empty($customer->address2)) {
                $addresses[] = ['value' => $customer->address2, 'label' => $customer->address2];
            }
        }

        return response()->json($addresses);
    }
}
