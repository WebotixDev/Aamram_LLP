<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QRCodeController extends Controller
{
    public function generateQR($id)
    {
        // Fetch outward details
        $outwardDetails = DB::table('outward_details')
            ->where('id', $id)
            ->select('order_no', 'size') // Fetch order_no and size ID
            ->first();

        if ($outwardDetails) {
            // Fetch size name
            $sizeDetails = DB::table('product_details')
                ->where('id', $outwardDetails->size)
                ->select('product_size') // Fetch product_size field
                ->first();

            $sizeName = $sizeDetails ? $sizeDetails->product_size : 'N/A';

            // Fetch batch history with product names
            $batchHistory = DB::table('batch_history')
                ->join('products', 'batch_history.productid', '=', 'products.id')
                ->where('batch_history.orderid', $outwardDetails->order_no)
                ->select('batch_history.*', 'products.product_name')
                ->get();
        } else {
            $batchHistory = collect(); // Empty collection
            $sizeName = 'N/A';
        }

        return view('admin.outward.show-qr', compact('id', 'batchHistory', 'sizeName'));
    }
}
