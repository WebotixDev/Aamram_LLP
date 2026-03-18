<?php

namespace App\Repositories\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class SupplierRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Supplier::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

        $services = implode(",",$request->products);

            $subjectData = [
                'user_id' =>    Auth::id(),
                'supplier_name'   =>$request->supplier_name,
                'mobile_no'   =>$request->mobile_no,
                'address'   =>$request->address,
                'gstin'   =>$request->gstin,
                'products'           => $services,

                'created_at'   => now(),
                'updated_at'   => now(),
                'update_id'    => Auth::id(),
            ];

            // Insert expense record
            Supplier::create($subjectData);

            DB::commit();
            return redirect()->route('admin.supplier.index')->with('success', __('Supplier Created Successfully'));
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

        $services = implode(",",$request['products']);

            DB::table('supplier')
                ->where('id', $id)
                ->update([

                     'supplier_name' =>$request['supplier_name'],
                     'mobile_no' =>$request['mobile_no'],
                     'address' =>$request['address'],
                     'gstin' =>$request['gstin'],
                                     'products'           => $services,

                    'update_id'    => Auth::id(),
                    'updated_at'   => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.supplier.index')->with('success', __('Supplier Updated Successfully'));
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
            $subject = Supplier::findOrFail($id);

            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Supplier Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
