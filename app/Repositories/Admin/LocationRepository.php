<?php

namespace App\Repositories\Admin;

use App\Models\Location;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class LocationRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Location::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {


            $subjectData = [
                'location'   =>$request->location,
                'mobile_no'   =>$request->mobile_no,
                'address'   =>$request->address,
               'purchase_manager' =>$request->purchase_manager,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            // Insert expense record
            Location::create($subjectData);

            DB::commit();
            return redirect()->route('admin.Location.index')->with('success', __('Location Created Successfully'));
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


            DB::table('location')
                ->where('id', $id)
                ->update([

                     'location' =>$request['location'],
                     'mobile_no' =>$request['mobile_no'],
                     'address' =>$request['address'],
                     'purchase_manager' =>$request['purchase_manager'],

                    'updated_at'   => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.Location.index')->with('success', __('location Updated Successfully'));
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
            $subject = location::findOrFail($id);

            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', __('location Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
