<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class ExpenseRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Expense::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
            $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');

            $expenseData = [
                'user_id' =>    Auth::id(),
                'exp_no'       => $request->exp_no,
                'expense_name'       => $request->expense_name,

                'billdate'         => $billDate,
                'exp_type'     => $request->exp_type,
                'inc_by'       => $request->inc_by,
                'date'         => $request->date,
                'mode'         => $request->mode,
                'cheque_no'    => $request->cheque_no,
                // 'season'   => $request->season,
                 'season'  => $season,
                'amt_pay'      => $request->amt_pay,
                'narration'    => $request->narration,
                'created_at'   => now(),
                'updated_at'   => now(),
                'update_id'    => Auth::id(),
            ];
  // Handle file upload if present
        if ($request->hasFile('expense_receipt')) {
            $file = $request->file('expense_receipt');
            $destinationPath = public_path('uploads/expense_receipt');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);

            $expenseData['expense_receipt'] = $fileName;
        }
            // Insert expense record
            Expense::create($expenseData);

            DB::commit();
            return redirect()->route('admin.expense.index')->with('success', __('Expense Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an expense entry.
     */
    public function update(array $request, $id)
    {
        DB::beginTransaction();
        try {

            $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
    
            DB::table('expense')
                ->where('id', $id)
                ->update([
                    'exp_no'       => $request['exp_no'],
                    'expense_name'       => $request['expense_name'],
                    'billdate'   => $billDate,
                    'exp_type'     => $request['exp_type'],
                    'inc_by'       => $request['inc_by'],
                    'date'     => $request['date'],
                    'mode'         => $request['mode'],
                    'cheque_no'    => $request['cheque_no'],
                    // 'season'   => $request['season'],
                     'season'  => $season,
                    'amt_pay'      => $request['amt_pay'],
                    'narration'    => $request['narration'],
                    'update_id'    => Auth::id(),
                    'updated_at'   => now(),
                ]);
                
         if (request()->hasFile('expense_receipt')) {
                $file = request()->file('expense_receipt');
                $destinationPath = public_path('uploads/expense_receipt');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Delete the old file if it exists
                $oldProduct = DB::table('expense')->where('id', $id)->first();
                if (!empty($oldProduct->expense_receipt) && file_exists(public_path($oldProduct->expense_receipt))) {
                    unlink(public_path($oldProduct->expense_receipt));
                }

                $file->move($destinationPath, $fileName);
                DB::table('expense')
                    ->where('id', $id)
                    ->update(['expense_receipt' =>  $fileName]);
            }


            DB::commit();
            return redirect()->route('admin.expense.index')->with('success', __('Expense Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an expense entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $expense = Expense::findOrFail($id);

            $expense->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Expense Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
