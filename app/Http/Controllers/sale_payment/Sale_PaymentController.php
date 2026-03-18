<?php

namespace App\Http\Controllers\sale_payment;

use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\DataTables\Sale_PaymentDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Sale_paymentRepository;
use Illuminate\Support\Facades\DB;

class Sale_PaymentController extends Controller
{
    protected $repository;

    public function __construct(Sale_paymentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the sale payment records.
     *
     * @param Sale_PaymentDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Sale_PaymentDataTable $dataTable)
    {
        return $dataTable->render('admin.sale_payment.index');
    }

    /**
     * Show the form for creating a new sale payment record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.sale_payment.create', [
            'products' => $this->getProducts(),
            'payment_methods' => $this->getPaymentMethods(),
        ]);
    }


    public function getCustomerRecords(Request $request)
    {
        $id = $request->input('customerId');
    
        // Ensure the ID is provided
       
    
        // Fetch records from the database
        $records = DB::table('sale_orderdetails')
            ->where('customer_name', $id)
            ->get(); // Use get() to fetch results


        

    
        $data = []; // Initialize the array
    
        foreach ($records as $info) {
            $Remaining = DB::table('purchase_payment_info')
            ->where('Invoicenumber', $info->id)
            ->sum('amount');

          if ($info->Tamount == $Remaining) {
                continue;
            }

            $amt= $info->Tamount - $Remaining;

       
            $data[] = [
                'Invoicenumber' => $info->id,
                'Tamount' => $amt,
            ];
        }
    
      
        return response()->json($data);
    }
    
    public function sale_payment_bill($id)
    {
        // Fetch sale order, sale order details, and customer details using joins
        $Sale_payment = DB::table('purchase_payments')
            ->where('purchase_payments.id', $id)
            ->leftJoin('sale_order', 'purchase_payments.id', '=', 'sale_order.pid')
            ->leftJoin('customers', 'purchase_payments.customer_name', '=', 'customers.id') // Join with the customers table
            ->leftJoin('states', 'customers.state_id', '=', 'states.id') // Join with states table
            ->leftJoin('districts', 'customers.district_id', '=', 'districts.id') // Join with districts table
            ->leftJoin('cities', 'customers.city_name', '=', 'cities.id') // Join with cities table
            ->select(
                'sale_order.*', 
                'purchase_payments.*', 
                'customers.customer_name', 
                'customers.mobile_no', 
                'customers.state_id', 
                'customers.district_id', 
                'customers.city_name',
                'states.name as state_name',
                'districts.district_name',
                'cities.name as city_name'
            )
            ->first();  // Use first() if you expect only one sale order for the given ID
    
        // Fetch the sale order details separately
        $Sale_paymentDetails = DB::table('purchase_payment_info')
            ->where('purchase_payment_info.pid', $Sale_payment->pid) // Assuming pid is the sale order ID
            ->get();
    
        // Pass the data to the view
        return view('admin.sale_payment.sale_payment_bill', compact('Sale_payment', 'Sale_paymentDetails'));
    }
    


    /**
     * Get a list of clients.
     *
     * @return \Illuminate\Support\Collection
     */
    // public function getClients()
    // {
    //     return \App\Models\Client::all()->pluck('client_name', 'id');
    // }

    /**
     * Get a list of products.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProducts()
    {
        return \App\Models\Product::all()->pluck('product_name', 'id');
    }

    /**
     * Get a list of available payment methods.
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        return ['Cash' => 'Cash', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer'];
    }

    /**
     * Store a newly created sale payment record and its details.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified sale payment record along with its details.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $salePayment = Sale_payment::with('details')->findOrFail($id);
        return view('admin.sale_payment.show', compact('salePayment'));
    }

    /**
     * Show the form for editing the sale payment record.
     *
     * @param Sale_payment $salePayment
     * @return \Illuminate\View\View
     */
    public function edit(Sale_payment $salePayment)
    {   
        
        
        $sale_details = Sale_payment::where('id', $salePayment->id)->first();
        $sale_payment = Sale_paymentDetails::where('pid', $salePayment->id)->get();
        $invoice = DB::table('purchase_payments')->where('id',  $salePayment->id)->first();

        return view('admin.sale_payment.edit', compact( 'salePayment','sale_details','sale_payment','invoice'));
    }
    


    /**
     * Update the specified sale payment record and its details.
     *
     * @param Request $request
     * @param Sale_payment $salePayment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sale_payment $salePayment)
    {
        return $this->repository->update($request->all(), $salePayment->id);
    }

    /**
     * Remove the specified sale payment record and its details from storage.
     *
     * @param Request $request
     * @param Sale_payment $salePayment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Sale_payment $salePayment)
    {
        return $this->repository->destroy($salePayment->id);
    }
    
      public function getData(Sale_PaymentDataTable $dataTable)
    {
        return $dataTable->ajax();
    }
}
