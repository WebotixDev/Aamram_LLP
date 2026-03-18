<?php

namespace App\Repositories\Admin;

use App\Models\Product_size;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class Product_sizeRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Product_size::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {


            $Product_sizeData = [
                'product_size'   =>$request->product_size,
                'status'   =>$request->status,
                'type'   =>$request->type,
            ];

            // Insert expense record
            Product_size::create($Product_sizeData);

            DB::commit();
            return redirect()->route('admin.product_size.index')->with('success', __('Product Size Created Successfully'));
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


            DB::table('product_size')
                ->where('id', $id)
                ->update([
                    'product_size' =>$request['product_size'],
                     'status' =>$request['status'],
                     'type' =>$request['type'],

                ]);

            DB::commit();
            return redirect()->route('admin.product_size.index')->with('success', __('Product Size Updated Successfully'));
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
    $subject = Product_size::findOrFail($id);

    // check product_details table
    $exists = DB::table('product_details')
        ->where('size_id', $subject->id)
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->with('error', 'This size already exists in product details, so it cannot be deleted.');
    }

    DB::beginTransaction();
    try {
        $subject->delete();
        DB::commit();

        return redirect()->back()->with('success', 'Product Size Deleted Successfully');
    } catch (Exception $e) {
        DB::rollback();
        throw $e;
    }
}
}
