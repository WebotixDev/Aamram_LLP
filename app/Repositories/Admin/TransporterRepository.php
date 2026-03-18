<?php

namespace App\Repositories\Admin;

use App\Models\Transporter;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class TransporterRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Transporter::class;
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
                'transporter'   =>$request->transporter,
                'mobile_no'   =>$request->mobile_no,
                'address'   =>$request->address,
                'gstin'   =>$request->gstin,

                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            // Insert expense record
            Transporter::create($subjectData);

            DB::commit();
            return redirect()->route('admin.Transporter.index')->with('success', __('Transporter Created Successfully'));
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


            DB::table('transporter')
                ->where('id', $id)
                ->update([

                     'transporter' =>$request['transporter'],
                     'mobile_no' =>$request['mobile_no'],
                     'address' =>$request['address'],
                     'gstin' =>$request['gstin'],
                    'updated_at'   => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.Transporter.index')->with('success', __('Transporter Updated Successfully'));
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
            $subject = Transporter::findOrFail($id);

            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Transporter Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
