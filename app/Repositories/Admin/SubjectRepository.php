<?php

namespace App\Repositories\Admin;

use App\Models\Subject;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class SubjectRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Subject::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {


            $subjectData = [
                'user_id' =>    Auth::id(),
                'expense_name'   =>$request->expense_name,
                'subject_name'   =>$request->subject_name,
                'created_at'   => now(),
                'updated_at'   => now(),
                'update_id'    => Auth::id(),
            ];

            // Insert expense record
            Subject::create($subjectData);

            DB::commit();
            return redirect()->route('admin.subject.index')->with('success', __('Expense Created Successfully'));
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


            DB::table('subjects')
                ->where('id', $id)
                ->update([
                    'expense_name' =>$request['expense_name'],
                     'subject_name' =>$request['subject_name'],
                    'update_id'    => Auth::id(),
                    'updated_at'   => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.subject.index')->with('success', __('Expense Updated Successfully'));
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
            $subject = Subject::findOrFail($id);

            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Expense Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
