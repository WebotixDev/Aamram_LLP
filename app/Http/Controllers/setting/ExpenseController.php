<?php

namespace App\Http\Controllers\setting;

use App\Models\Expense;
use App\DataTables\ExpenseDataTable;
use Illuminate\Http\Request;
use App\Repositories\ExpenseRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    protected $repository;

    public function __construct(ExpenseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the expense records.
     *
     * @param ExpenseDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(ExpenseDataTable $dataTable)
    {
        return $dataTable->render('admin.expense.index');
    }

    /**
     * Show the form for creating a new expense record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.expense.create');
    }

    /**
     * Store a newly created expense record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified expense record.
     *
     * @param Expense $expense
     * @return \Illuminate\View\View
     */
    public function show(Expense $expense)
    {
        return view('admin.expense.show', ['expense' => $expense]);
    }


    public function getExpenseNames(Request $request)
{
    $expenseId = $request->expense_id;

    // Fetch subjects related to the selected expense category
    $subjects = DB::table('subjects')
        ->where('expense_name', $expenseId) // Assuming `subjects` table has `expense_category_id`
        ->get();


    return response()->json($subjects);
}



public function expensess(Request $request)
{

    try {


        $customerId = DB::table('subjects')->insertGetId([
            'user_id' => Auth::id(),
            'expense_name' => $request->input('expense_namess'),
            'subject_name' => $request->input('subject_name'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customerId,
                'subject_name' => $request->input('subject_name'),
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
     * Show the form for editing the specified expense record.
     *
     * @param Expense $expense
     * @return \Illuminate\View\View
     */
    public function edit(Expense $expense)
    {
        $invoice = DB::table('expense')->where('id',  $expense->id)->first();
        return view('admin.expense.edit', ['expense' => $expense], compact('invoice'));
    }

    /**
     * Update the specified expense record.
     *
     * @param Request $request
     * @param Expense $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $expense)
    {
        return $this->repository->update($request->all(), $expense);
    }

    /**
     * Remove the specified expense record from storage.
     *
     * @param Expense $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Expense $expense)
    {
        return $this->repository->destroy($expense->id);
    }
}
