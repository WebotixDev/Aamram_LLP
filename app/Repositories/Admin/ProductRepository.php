<?php

namespace App\Repositories\Admin;


use App\Models\Product;
use App\Models\Product_details;
use App\Models\Product_size;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Product::class;
    }

    /**
     * Store a new product along with its details in the database.
     */

     public function store($request)
     {
         DB::beginTransaction();
         try {
             $product = [
                 'user_id'      => Auth::id(),
                 'update_id'    => Auth::id(),
                 'product_name' => $request->product_name,
                 'moq'          => $request->moq,
                 'description'  => $request->description,
                 'cgst'         => $request->cgst,
                 'sgst'         => $request->sgst,
                 'igst'         => $request->igst,
                 'status'      =>$request->status,
                 'type'   => $request->type,
                 'created_at'   => now()->format('Y-m-d'),
                 'updated_at'   => now()->format('Y-m-d'),
             ];

             // Handle Image Upload
             if ($request->hasFile('img')) {
                 $file = $request->file('img');
                 $destinationPath = public_path('uploads/product_img');
                 $fileName = time() . '_' . $file->getClientOriginalName();
                 $file->move($destinationPath, $fileName);

                 // Assigning correct key to store image in DB
                 $product['img'] = 'uploads/product_img/' . $fileName;
             }

             // Insert and get the ID
             $productid = Product::insertGetId($product);

             $cnt = $request['cnt'];

for ($i = 1; $i <= $cnt; $i++) {

    $size_id = $request['product_size' . $i] ?? null;
    $dist_price = $request['dist_price' . $i] ?? null;
    $purch_price = $request['purch_price' . $i] ?? null;
    $discount = $request['discount' . $i] ?? 0;
    $status = $request['status' . $i] ?? 0;
    $sizeoff = $request['sizeoff' . $i] ?? 0;

    // Fetch size name using ID
    $size = Product_size::find($size_id);
    $size_name = $size ? $size->product_size : null;

    $productData = [
        'user_id'      => Auth::id(),
        'parentID'     => $productid,
        'size_id'      => $size_id,          // ✅ Store ID
        'product_size' => $size_name,        // ✅ Store Name
        'dist_price'   => $dist_price,
        'purch_price'  => $purch_price,
        'discount'     => $discount,
        'status'       => $status,
        'sizeoff'      => $sizeoff,
        'update_id'    => Auth::id(),
        'created_at'   => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
        'updated_at'   => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
    ];

    Product_details::create($productData);
}

             DB::commit();
             return redirect()->route('admin.product.index')->with('success', __('Product Created Successfully'));
         } catch (Exception $e) {
             DB::rollback();
             throw $e;
         }
     }



    /**
     * Update a product and its details in the database.
     */
    public function update(array $request, $id)
    {

        // dd($request);
        // die;


        DB::beginTransaction();
        try {
            // Update product data
            DB::table('products')
                ->where('id', $id) // Fixed operator syntax
                ->update([
                    'update_id'    => Auth::id(),
                    'product_name' => $request['product_name'] ?? null,
                    'moq'          => $request['moq'] ?? null,
                    'description'  => $request['description'] ?? null,
                    'cgst'         => $request['cgst'] ?? 0, // Fixed incorrect assignment
                    'sgst'         => $request['sgst'] ?? 0,
                    'igst'         => $request['igst'] ?? 0,
                    'status'       =>$request['status'],
                     'type'  => $request['type'] ?? null,
                    'updated_at'   => now()->format('Y-m-d H:i:s'),
                ]);

            // Handling image upload
            if (request()->hasFile('img')) {
                $file = request()->file('img');
                $destinationPath = public_path('uploads/product_img');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Delete the old file if it exists
                $oldProduct = DB::table('products')->where('id', $id)->first();
                if (!empty($oldProduct->img) && file_exists(public_path($oldProduct->img))) {
                    unlink(public_path($oldProduct->img));
                }

                $file->move($destinationPath, $fileName);
                DB::table('products')
                    ->where('id', $id)
                    ->update(['img' => 'uploads/product_img/' . $fileName]);
            }

// Fetch existing product IDs for the given parentID
      $existingIDs = Product_details::where('parentID',$id)
                        ->where('disable','!=',1)
                        ->pluck('id')
                        ->toArray();

$cnt = $request['cnt'] ?? 0; // Ensure $cnt is set
$requestProductIDs = []; // To store product IDs from the request

for ($i = 1; $i <= $cnt; $i++) {

    $productID = $request['proid' . $i] ?? null;
    $product_size = $request['product_size' . $i] ?? null;
    $size_id = $request['product_size' . $i] ?? null; // ID from dropdown
    $dist_price   = $request['dist_price' . $i] ?? null;
    $purch_price  = $request['purch_price' . $i] ?? null;
    $original_price = $request['original_price' . $i] ?? null;
    $discount = $request['discount' . $i] ?? 0;
    $web_price = $request['web_price' . $i] ?? 0;
    $status = $request['status' . $i] ?? 0;
    $sizeoff = $request['sizeoff' . $i] ?? 0;

    $size = Product_size::find($size_id);
    $size_name = $size ? $size->product_size : null;
    if ($productID) {
        $requestProductIDs[] = $productID; // Track IDs from the request

       $existingRecord = Product_details::where('id',$productID)
                                    ->where('disable','!=',1)
                                    ->first();
        if ($existingRecord) {
            // Update record if it exists
            $existingRecord->update([
               'size_id'        => $size_id,      // Store ID
               'product_size'   => $size_name,    // Store name
               'dist_price'      => $dist_price,
                'purch_price'     => $purch_price,
                'discount'        => $discount,
                'status'          => $status ,
                 'sizeoff'     =>$sizeoff,
                'updated_at'      => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            ]);
        }
    } else {
        // Insert new record
        $newRecord = Product_details::create([
            'user_id'       => Auth::id(),
            'parentID'      => $id,
           'size_id'        => $size_id,
            'product_size'   => $size_name,
            'dist_price'    => $dist_price,
            'purch_price'   => $purch_price,
            'discount'      => $discount,
            'status'          => $status ,
            'update_id'     => Auth::id(),
            'sizeoff'     =>$sizeoff,
            'created_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            'updated_at'    => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
        ]);

        // Track new inserted ID
        $requestProductIDs[] = $newRecord->id;
    }
}

// Delete records that are in the database but NOT in the request
$idsToDelete = array_diff($existingIDs, $requestProductIDs);
     Product_details::whereIn('id',$idsToDelete)
            ->where('disable','!=',1)
            ->delete();


            DB::commit();
            return redirect()->route('admin.product.index')->with('success', __('Product Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', __('Error: ') . $e->getMessage());
        }
    }



    /**
     * Delete a product and its associated details from the database.
     */
   public function destroy($id)
{
    $product = $this->model->findOrFail($id);

        // Check if the product is used in any sale orders
        $isUsed = DB::table('sale_order')
            ->where('services', $id)
            ->exists();

        if ($isUsed) {
            return redirect()->back()->with('error', 'This product is already used in Sale Order. Cannot delete.');
        }
    DB::beginTransaction();

    try {

        // Delete associated product details
        Product_details::where('parentID', $product->id)->delete();

        // Delete the product
        $product->delete();

        DB::commit();

        return redirect()->back()->with('success', 'Product Deleted Successfully');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
}
