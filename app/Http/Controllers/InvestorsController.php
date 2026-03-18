<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;
use App\DataTables\InvestorDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\InvestorsRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InvestorsController extends Controller
{
    protected $repository;

    public function __construct(InvestorsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the sale payment records.
     *
     * @param Sale_PaymentDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index($investorId = null)
    {
        return view('admin.investors.main');
    }

    public function show($investorId)
    {
        $dataTable = new InvestorDataTable($investorId);

        return $dataTable->render('admin.investors.list');
    }


    /**
     * Show the form for creating a new sale payment record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.investors.create', [
            'products' => $this->getProducts(),
            'payment_methods' => $this->getPaymentMethods(),
        ]);
    }


    public function investorAdd(Request $request)
    {

        try {


            $InvestorsId = DB::table('investors_name')->insertGetId([
                'user_id' => Auth::id(),
                'investors_name' => $request->input('name'),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $InvestorsId,
                    'investors_name' => $request->input('name'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the expense.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


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
    // public function show($id)
    // {
    //     $salePayment = Investor::findOrFail($id);
    //     return view('admin.investors.show', compact('salePayment'));
    // }

    /**
     * Show the form for editing the sale payment record.
     *
     * @param Sale_payment $salePayment
     * @return \Illuminate\View\View
     */
    public function edit(Investor $investor)
    {
        $invoice = DB::table('investors_payment')->where('id', $investor->id)->first();


        // Calculate paid and receive sums
        $paidSum = DB::table('investors_payment')
            ->where('investor_name', $investor->investor_name)
            ->where('type', 'paid')
            ->sum('amt_pay');

            $paidTotalsum = DB::table('investors_payment')
             ->where('investor_name', $investor->investor_name)
             ->where('type', 'paid')
             ->sum('amt_pay');


        $recieveSum = DB::table('investors_payment')
            ->where('investor_name', $investor->investor_name)
            ->where('type', 'receive')
            ->sum('amt_pay');

        $balance = $recieveSum - $paidSum;


        return view('admin.investors.edit', compact('investor', 'invoice','balance', 'paidSum' ));
    }




    /**
     * Update the specified sale payment record and its details.
     *
     * @param Request $request
     * @param Sale_payment $salePayment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Investor $investor)
    {
        return $this->repository->update($request->all(), $investor->id);
    }


    /**
     * Remove the specified sale payment record and its details from storage.
     *
     * @param Request $request
     * @param Sale_payment $salePayment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Investor $investor)
    {
        return $this->repository->destroy($investor->id);
    }


    public function investoreBalance($id)
{
    $investor = DB::table('investors_payment')->where('investor_name', $id)->first();

    if (!$investor) {
        return response()->json([
            'status' => 'not_found',
            'balance' => 0,
        ]);
    }

    // Initialize sums
    $paidSum = 0;
    $recieveSum = 0;

    // Calculate only for selected investor based on type
    $paidSum = DB::table('investors_payment')
    ->where('investor_name', $id)
    ->where('type', 'paid')
    ->sum('amt_pay');



// Sum of amt_pay where investor matches and type is 'recieve'
$recieveSum = DB::table('investors_payment')
        ->where('investor_name', $id)
        ->where('type', 'receive')
        ->sum('amt_pay');


    $balance =  $recieveSum - $paidSum ;

    return response()->json([

        'balance' => $balance,
    ]);
}

}
