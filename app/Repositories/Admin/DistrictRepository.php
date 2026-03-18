<?php

namespace App\Repositories\Admin;

use App\Models\District;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class DistrictRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    function model()
    {
        return District::class;
    }

    /**
     * Store a new district in the database.
     */
    public function store(Request $request)
{


    DB::beginTransaction();
    try {
        $category =  $this->model->create([
            'district_name' => $request->district_name,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::commit();

        return to_route('admin.district.index')->with('success', __('District Created Successfully'));

    } catch (Exception $e) {

        DB::rollback();
        throw $e;
    }


}


    /**
     * Update an existing district in the database.
     */
    public function update($request, $id)
    {

        DB::beginTransaction();
        try {
            $ground = $this->model->findOrFail($id);
            $ground->update($request);

            DB::commit();
            return redirect()->route('admin.district.index')->with('success', __('District Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            // Optional: Log the exception for debugging
            // Log::error('Update failed for sports season: '.$e->getMessage());
            throw $e;
        }


    }

    /**
     * Delete a district from the database.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $district = $this->model->findOrFail($id);
            $district->delete(); // Use delete() instead of destroy() as it's an instance method

            DB::commit();
            return redirect()->back()->with('success', __('District Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
