<?php

namespace App\Http\Controllers;

use App\Models\sale_order;
use App\Models\sale_details;
use App\Models\Sale_payment;
use App\Models\Sale_paymentDetails;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\Customer_saleBillDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Sale_orderRepository;
use app\Helpers\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class Customer_saleBillController extends Controller
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
    public function index(Customer_saleBillDataTable $dataTable)
    {
        return $dataTable->render('admin.Customer_bill.index');
    }

    /**
     * Show the form for creating a new sale order.
     *
     * @return \Illuminate\View\View
     */


    /**
     * Retrieve all customers for the dropdown.
     *
     * @return \Illuminate\Support\Collection
     */

public function getData(Customer_saleBillDataTable $dataTable)
    {
        return $dataTable->ajax();
    }


    public function saleBill($id)
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
                'customers.pin_code',
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
        return view('admin.sale_order.bill', compact('saleOrder', 'saleOrderDetails'));
    }








    public function Customer_Bill(Request $request)
    {
        $selectedRows = explode(',', $request->input('selected_rows', ''));

        if (empty($selectedRows) || count($selectedRows) === 0) {
            return redirect()->back()->with('error', 'Please select at least one record.');
        }

        // Get unique sale orders
        $saleOrders = DB::table('sale_orderdetails')
            ->leftJoin('sale_order', 'sale_orderdetails.id', '=', 'sale_order.pid')
            ->leftJoin('customers', 'sale_orderdetails.customer_name', '=', 'customers.id')
            ->leftJoin('states', 'customers.state_id', '=', 'states.id')
            ->whereIn('sale_orderdetails.id', $selectedRows)
            ->select(
                'sale_order.pid',
                'sale_orderdetails.billdate',
                'sale_orderdetails.Tamount',
                'sale_orderdetails.*',
                'customers.id as customer_id',
                'customers.customer_name',
                'customers.mobile_no',
                'customers.city_name',
                'customers.district_id',
                'customers.address',
                'states.name as state_name'
            )
        ->orderBy('sale_orderdetails.billdate', 'asc') // ✅ sort by billdate ascending
            ->get();

        $uniqueSaleOrders = $saleOrders->unique('pid');
        $pids = $uniqueSaleOrders->pluck('pid')->unique();

        // Get associated product details
        $details = DB::table('sale_order')
            ->leftJoin('products', 'sale_order.services', '=', 'products.id')
            ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id')
            ->select(
                'sale_order.pid',
                'products.product_name',
                'product_details.product_size',
                'sale_order.qty',
                'sale_order.*'
            )
            ->whereIn('sale_order.pid', $pids)
            ->get();

        $groupedDetails = $details->groupBy('pid');

        // Group sale orders by customer_id
        $groupedOrdersByCustomer = $uniqueSaleOrders->groupBy('customer_id');

        // 🧾 Get payments for each pid
        $paymentData = DB::table('purchase_payment_info')
            ->whereIn('invoicenumber', $pids)
            ->select('invoicenumber', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('invoicenumber')
            ->get()
            ->keyBy('invoicenumber'); // so you can easily access by pid

        // 🧠 Prepare payments with due calculation
        $paymentSummary = [];

        foreach ($uniqueSaleOrders as $order) {
            $pid = $order->pid;
            $paid = $paymentData[$pid]->total_paid ?? 0;
            $due = $order->Tamount - $paid;

            $paymentSummary[$pid] = [
                'paid' => $paid,
                'due' => $due,
            ];
        }
                // Grouping for wholesaler-wise summary
                $wholesalerGrand = [
                    'total' => 0,
                    'paid' => 0,
                    'due' => 0,
                ];
                
                foreach ($groupedOrdersByCustomer as $customerId => $orders) {
                    foreach ($orders as $order) {
                        $summary = $paymentSummary[$order->pid] ?? ['paid' => 0, 'due' => 0];
                        $wholesalerGrand['total'] += $order->Tamount;
                        $wholesalerGrand['paid'] += $summary['paid'];
                        $wholesalerGrand['due'] += $summary['due'];
                    }
                }
        return view('admin.Customer_bill.Bulk_customer_bill', compact(
            'groupedOrdersByCustomer',
            'groupedDetails',
            'paymentSummary',
             'wholesalerGrand',

        ));
    }




    /**
     * Remove the specified sale order and its details from storage.
     *
     * @param Request $request
     * @param sale_order $saleOrder
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $saleOrder)
    {
        return $this->repository->destroy($saleOrder);
    }
}
