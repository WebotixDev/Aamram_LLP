<?php

namespace App\Repositories\Admin;

use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class CustomerRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Customer::class;
    }

    /**
     * Store a new customer in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $customer = $this->model->create([
                'customer_name' => $request->customer_name,
                'company_name' => $request->company_name,
                'mobile_no' => $request->mobile_no,
                 'wp_number' => $request->wp_number,
                'vendor' => $request->vendor,
                'email_id' => $request->email_id,
                'address' => $request->address,
                 'address1' => $request->address1,
                'address2' => $request->address2,
                'city_name' => $request->city_name,
                'pin_code' => $request->pin_code,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.customer.index')->with('success', __('Customer Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

  
    public function update($request, $id)
    {

        DB::beginTransaction();
        try {
            $customer = $this->model->findOrFail($id);
            $customer->update($request);
    
            DB::commit();
            return redirect()->route('admin.customer.index')->with('success', __('Customer Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            // Optional: Log the exception for debugging
            // Log::error('Update failed for sports season: '.$e->getMessage());
            throw $e;
        }


    }

    /**
     * Delete a customer from the database.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $customer = $this->model->findOrFail($id);
            $customer->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Customer Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
