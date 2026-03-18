<?php

namespace App\Repositories;

use App\Models\Product_bulk;
use App\Repositories\Log;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class Product_bulkRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Product_bulk::class;
    }

    /**
     * Store a new product_bulk entry in the database.
     */
    public function store(Request $request)
    {  

      DB::beginTransaction();
      try {
   // Loop through each product's sizes and rates from the request data
   foreach ($request->input('product') as $product_id => $details) {
    foreach ($details['size'] as $index => $size) {
        $rate = $details['rate'][$index];

        // Update the product_details table
        DB::table('product_details')
            ->where('parentID', $product_id)
            ->where('product_size', $size)
            ->update([
                'purch_price' => $rate,
                'updated_at'  => now(),
                'update_id'   => Auth::id(),
            ]);

      
        }
    }

foreach ($request->input('product') as $product_id => $details) {
    foreach ($details['size'] as $index => $size) {

// $one[] =$size;
        $UpdateSize = DB::table('product_details')
        ->where('parentID', $product_id)
        ->where('product_size', $size)
        ->select('id') // Fetch order_no and size ID
        ->value('id'); // Pass 'product_size' column as an argument
        $rate = $details['rate'][$index];

        // Update the product_details table
        DB::table('purchase_product')
            ->where('services', $product_id)
            ->where('size', $UpdateSize)
            ->update([
                'rate' => $rate,
              
            ]);
    }

}


          DB::commit();
          return redirect()->route('admin.product_bulk.index')->with('success', __('Product Bulk Updated Successfully'));
      } catch (Exception $e) {
          DB::rollback();
          throw $e;
      }
    }

    /**
     * Update a product_bulk entry.
     */
    public function update(array $request, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('product_bulk')
                ->where('id', $id)
                ->update([
                    'size'        => $request['size'],
                    'rate'        => $request['rate'],
                    'services'    => $request['services'],
                    'updated_at'  => now(),
                    'update_id'   => Auth::id(),
                ]);

            DB::commit();
            return redirect()->route('admin.product_bulk.index')->with('success', __('Product Bulk Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a product_bulk entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $productBulk = Product_bulk::findOrFail($id);

            $productBulk->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Product Bulk Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
