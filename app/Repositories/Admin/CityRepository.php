<?php

namespace App\Repositories\Admin;

use App\Models\City;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class CityRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    function model()
    {
        return City::class;
    }

    /**
     * Store a new city in the database.
     */
    public function store(Request $request)
    {
      //  DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $city = $this->model->create([
                'name' => $request->name,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'created_at' => now(),
                'updated_at' => now(),

            ]);
//dd(DB::getQueryLog());
            DB::commit();
            return redirect()->route('admin.city.index')->with('success', __('City Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing city in the database.
     */
    public function update($request, $id)
    {

        DB::beginTransaction();
        try {
            $ground = $this->model->findOrFail($id);
            $ground->update($request);
    
            DB::commit();
            return redirect()->route('admin.city.index')->with('success', __('District Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            // Optional: Log the exception for debugging
            // Log::error('Update failed for sports season: '.$e->getMessage());
            throw $e;
        }


    }
    

    /**
     * Delete a city from the database.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $city = $this->model->findOrFail($id);
            $city->delete(); // Use delete() since it's an instance method.

            DB::commit();
            return redirect()->back()->with('success', __('City Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
