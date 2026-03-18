<?php

namespace App\Http\Controllers\Outward;

use App\Models\Delivery_Challan;
use App\Models\Delivery_ChallanDetails;
use App\Repositories\Delivery_ChallanRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\Delivery_ChallanDataTable;

class Delievry_ChallanController extends Controller
{
    protected $repository;

    public function __construct(Delivery_ChallanRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of delivery challans.
     */
    public function index(Delivery_ChallanDataTable $dataTable)
    {
        return $dataTable->render('admin.Delivery_Challan.index');
    }

    /**
     * Show the form for creating a new delivery challan.
     */
    public function create()
    {
        return view('admin.Delivery_Challan.create');
    }

    /**
     * Store a newly created delivery challan.
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified delivery challan.
     */
    public function show(Delivery_Challan $Delivery_Challan)
    {
        return view('admin.Delivery_Challan.show', compact('Delivery_Challan'));
    }

    /**
     * Show the form for editing the specified delivery challan.
     */
     
   public function edit($id)
    {
        // Fetch the Delivery Challan record
        $Delivery_Challan = DB::table('delivery_challan')->where('id', $id)->first();

        // Ensure the Delivery Challan exists
        if (!$Delivery_Challan) {
            return redirect()->back()->with('error', 'Delivery Challan not found.');
        }

        // Fetch all related otid values as an array
        $selected_ids = DB::table('delivery_challan_details')
                            ->where('pid', $id)
                            ->pluck('otid') // Fetch only the otid column
                            ->toArray(); // Convert to an array for easy use in Blade

        return view('admin.Delivery_Challan.edit', compact('Delivery_Challan', 'selected_ids'));
    }

    /**
     * Update the specified delivery challan.
     */
    public function update(Request $request, $id)
    {
        return $this->repository->update($request, $id);
    }

    /**
     * Remove the specified delivery challan.
     */
    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }



    public function challanBill($id)
    {
        // Fetch the sale order using joins and ensure it exists
        $saleOrder = DB::table('delivery_challan')
            ->where('delivery_challan.id', $id)
            ->leftJoin('delivery_challan_details', 'delivery_challan.id', '=', 'delivery_challan_details.pid')
            ->select('delivery_challan_details.*', 'delivery_challan.*')
            ->first();

        if (!$saleOrder) {
            return redirect()->back()->with('error', 'Challan not found.');
        }

        // Fetch the sale order details separately
        $saleOrderDetails = DB::table('delivery_challan_details')
        ->where('delivery_challan_details.pid', $saleOrder->id)
        ->leftJoin('products', 'delivery_challan_details.services', '=', 'products.id')
        ->leftJoin('product_details', 'delivery_challan_details.size', '=', 'product_details.id')
        ->leftJoin('customers', 'delivery_challan_details.customer_name', '=', 'customers.id')
        ->leftJoin('sale_orderdetails', 'delivery_challan_details.order_no', '=', 'sale_orderdetails.id')
        ->leftJoin('states', 'customers.state_id', '=', 'states.id')
        ->select(
            'delivery_challan_details.*',
            'products.product_name',
            'product_details.product_size',
            'customers.customer_name',
            'customers.mobile_no',
            'customers.pin_code',
            'customers.address',
            'customers.state_id',
            'customers.district_id',
            'customers.city_name',
          'sale_orderdetails.order_address',
            'states.name as state_name'
        )
        ->orderBy('delivery_challan_details.customer_name')
        ->get()
        ->groupBy('customer_name'); // Group by customer
        // Pass data to the view
        return view('admin.Delivery_Challan.challan_bill', compact('saleOrder', 'saleOrderDetails'));
    }



    public function customerChllan($id)
    {
        // Fetch the sale order using joins and ensure it exists
        $saleOrder = DB::table('delivery_challan')
            ->where('delivery_challan.id', $id)
            ->leftJoin('delivery_challan_details', 'delivery_challan.id', '=', 'delivery_challan_details.pid')
            ->select('delivery_challan_details.*', 'delivery_challan.*')
            ->first();

        if (!$saleOrder) {
            return redirect()->back()->with('error', 'Challan not found.');
        }

        // Fetch the sale order details separately
        $saleOrderDetails = DB::table('delivery_challan_details')
        ->where('delivery_challan_details.pid', $saleOrder->id)
        ->leftJoin('products', 'delivery_challan_details.services', '=', 'products.id')
        ->leftJoin('product_details', 'delivery_challan_details.size', '=', 'product_details.id')
        ->leftJoin('customers', 'delivery_challan_details.customer_name', '=', 'customers.id')
        ->leftJoin('sale_orderdetails', 'delivery_challan_details.order_no', '=', 'sale_orderdetails.id')
        ->leftJoin('states', 'customers.state_id', '=', 'states.id')
        ->select(
            'delivery_challan_details.*',
            'products.product_name',
            'product_details.product_size',
            'customers.customer_name',
            'customers.mobile_no',
            'customers.pin_code',
            'customers.address',
            'customers.state_id',
            'customers.district_id',
            'customers.city_name',
            'sale_orderdetails.order_address',

            'states.name as state_name'
        )
        ->orderBy('delivery_challan_details.customer_name')
        ->get()
        ->groupBy('customer_name'); // Group by customer
        // Pass data to the view
        return view('admin.Delivery_Challan.Customer_bill', compact('saleOrder', 'saleOrderDetails'));
    }

}
