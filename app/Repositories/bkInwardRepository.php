<?php

namespace App\Repositories;

use App\Models\inward;
use App\Models\inward_details;
use app\Models\Product;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class InwardRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return inward::class;
    }

    /**
     * Store a new inward record along with its details in the database.
     */
    public function store(Request $request)
    {
     

        $billDate = Carbon::createFromFormat('d-m-Y', $request->billdate)->format('Y-m-d');


                $count = DB::table('purchase_details')->count();

                $batch_id = $count > 0 ? $count + 1 : 1;


        DB::beginTransaction();
        try {
            $inward = [
                'user_id'    => Auth::id(),
                'PurchaseDate' => now()->format('Y-m-d'),
                'billdate'    => $billDate,
                'batch_id'    => $batch_id,
                'product'  => $request->product,
                'product_size' => $request->product_size,
                'stock'  => $request->stock,
                'qty'  =>$request->qty,
                'Tquantity'   => $request->totalproamt,
                'Invoicenumber' => $request->Invoicenumber,
                'update_id'   => Auth::id(),
                'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
            ];

                $inwardId = inward::insertGetId($inward);

                $cnt = $request['cnt'];

                    for ($i = 1; $i <= $cnt; $i++) {
                        //$service = $request['services' . $i] ?? null;
                        $size = $request['size' . $i] ?? null;
                        $stage = $request['stage' . $i] ?? null;
                        $quantity = $request['quantity' . $i] ?? 0;
                        $rate = $request['rate' . $i] ?? 0;
                        $productsizes = $request['productsizes' . $i] ?? 0;


                        $inwardDetailData = [
                            'user_id'    => Auth::id(),
                            'pid'        => $inwardId, // Assuming you have the $id available
                            'services'   => $request->product,
                            'size'       => $size,
                            'stage'      => $stage,
                            'Quantity'   => $quantity,
                            'batch_id'    => $batch_id,
                            'rate'       => $rate,
                            'productsizes' => $productsizes,
                            'update_id'  => Auth::id(),
                            'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                            'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        ];

                        inward_details::create($inwardDetailData);
                    }


                            DB::commit();
                            return redirect()->route('admin.inward.index')->with('success', __('Inward Created Successfully'));
                        } catch (Exception $e) {
                            DB::rollback();
                            throw $e;
                        }
                    }

    /**
     * Update an inward record and its details.
     */
    // public function update(array $request, $id)
    // {

    //     DB::beginTransaction();
    //     try {

    //         $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');

    //                 // Update the `inwards` table directly
    //                 DB::table('purchase_details')
    //                 ->where('id', operator: $id)
    //                 ->update([
    //                     'billdate'   => $billDate,
    //                     'Tquantity'  => $request['totalproamt'],
    //                     'product'  => $request['product'],
    //                     'product_size'  => $request['product_size'],
    //                     'stock'  => $request['stock'],
    //                     'qty'  => $request['qty'],
    //                     'update_id'  => Auth::id(),
    //                     'updated_at' => now(),
    //                 ]);


    //         if (!empty($request['size1'])) {

    //             inward_details::where(column: 'pid', operator: $id)->delete();
    //             // Loop over the count (cnt) to handle services
    //             $cnt = $request['cnt'];

    //             for ($i = 1; $i <= $cnt; $i++) {
    //                 // Get the values for each index
    //                 ///$service = $request['services' . $i] ?? null;
    //                 $size = $request['size' . $i] ?? null;
    //                 $stage = $request['stage' . $i] ?? null;
    //                 $quantity = $request['quantity' . $i] ?? 0;
    //                 $rate = $request['rate' . $i] ?? 0;
    //                 $productsizes = $request['productsizes' . $i] ?? 0;


    //                 // Prepare data for insertion
    //                 $inwardDetailData = [
    //                     'user_id'    => Auth::id(),
    //                     'pid'        => $id, // Assuming you have the $id available
    //                     'services'   => $request['product'],
    //                     'size'       => $size,
    //                     'stage'      => $stage,
    //                     'Quantity'   => $quantity,
    //                     'rate'       => $rate,
    //                     'productsizes' => $productsizes,
    //                     'update_id'  => Auth::id(),
    //                     'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
    //                     'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
    //                 ];


    //                 // Insert the record into inward_details table
    //                 inward_details::create($inwardDetailData);
    //             }
    //         }


    //         DB::commit();
    //         return redirect()->route('admin.inward.index')->with('success', __('Inward Updated Successfully'));
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }
    
     public function update(array $request, $id)
    {

        DB::beginTransaction();
        try {


            $existingBatchId = DB::table('purchase_details')
            ->where('id', $id)
            ->value('batch_id'); // Get the batch_id directly

            $billDate = Carbon::createFromFormat('d-m-Y', $request['billdate'])->format('Y-m-d');

                    // Update the `inwards` table directly
                    DB::table('purchase_details')
                    ->where('id', operator: $id)
                    ->update([
                        'billdate'   => $billDate,
                        'Tquantity'  => $request['totalproamt'],
                        'product'  => $request['product'],
                        'product_size'  => $request['product_size'],
                        'stock'  => $request['stock'],
                        'qty'  => $request['qty'],
                        'batch_id'  => $existingBatchId,

                        'update_id'  => Auth::id(),
                        'updated_at' => now(),
                    ]);


            if (!empty($request['size1'])) {

                inward_details::where(column: 'pid', operator: $id)->delete();
                // Loop over the count (cnt) to handle services
                $cnt = $request['cnt'];

                for ($i = 1; $i <= $cnt; $i++) {
                    // Get the values for each index
                    ///$service = $request['services' . $i] ?? null;
                    $size = $request['size' . $i] ?? null;
                    $stage = $request['stage' . $i] ?? null;
                    $quantity = $request['quantity' . $i] ?? 0;
                    $rate = $request['rate' . $i] ?? 0;
                    $productsizes = $request['productsizes' . $i] ?? 0;


                    // Prepare data for insertion
                    $inwardDetailData = [
                        'user_id'    => Auth::id(),
                        'pid'        => $id, // Assuming you have the $id available
                        'services'   => $request['product'],
                        'size'       => $size,
                        'stage'      => $stage,
                        'Quantity'   => $quantity,
                        'rate'       => $rate,
                        'productsizes' => $productsizes,
                        'batch_id'  => $existingBatchId,
                        'update_id'  => Auth::id(),
                        'created_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                        'updated_at' => now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    ];


                    // Insert the record into inward_details table
                    inward_details::create($inwardDetailData);
                }
            }


            DB::commit();
            return redirect()->route('admin.inward.index')->with('success', __('Inward Updated Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    /**
     * Delete an inward record and its associated inward details.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $inward = $this->model->findOrFail($id);
            // Delete associated inward details
            inward_details::where('pid', $inward->id)->delete();
            // Delete the inward record
            $inward->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Inward Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
