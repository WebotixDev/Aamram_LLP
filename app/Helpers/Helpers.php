<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Country;
use App\Models\Attachment;
use App\Models\SportsSeason;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Permission as ModelsPermission;

use App\Models\farm_inward_details;
use App\Models\Farm_Delivery_challan_details;
class Helpers
{
    public static function isUserLogin()
    {
        return auth()?->check();
    }

    public static function getCurrentUserId()
    {
      if (self::isUserLogin()) {
        return auth()?->user()?->id;
      }
    }

    public static function getMedia($id)
    {
      return Attachment::find($id);
    }

    public static function getCountryCode(){
      return Country::get(["calling_code", "id", "iso_3166_2", 'flag'])->unique('calling_code');
    }

    public static function getUser()
    {
        $user = User::with('roles')->where('system_reserve' ,'!=', 1)->latest()->take(5)->get();
        return $user;
    }


    // public static function getstockbatch($ID,$Date,$batchid)
    // {
    //     $pquantity = DB::table('purchase_product')
    //         ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
    //         ->where('purchase_product.services', $ID)
    //         ->where('purchase_details.batch_id', $batchid)
    //         ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
    //          ->sum('purchase_product.Quantity');

    //     $res = $pquantity;

    //     return $res;
    // }



    public static function getstockbatch($ID,$size,$stage,$Date)
    {

        $season = session('selected_season');

        $pquantity = DB::table('purchase_product')
            ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
            ->where('purchase_product.services', $ID)
            ->where('purchase_product.size',$size)
            ->where('purchase_product.stage', $stage)
            ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
            ->where('purchase_details.season',$season)
             ->sum('purchase_product.Quantity');


       $squantity = DB::table('sale_order')
             ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
              ->where('sale_order.services', $ID)
              ->where('sale_order.size',$size)
              ->where('sale_order.stage', $stage)
             ->where('sale_orderdetails.season',$season)
             ->whereDate('sale_orderdetails.order_date', '<=', $Date)
             ->sum('sale_order.qty');


        $res = $pquantity-$squantity;

        return $res;
    }

    public static function getstockpeti($ID,$Date)
    {


             $season = session('selected_season');
        $peticount = DB::table('farm_inward_details')
            ->join('farm_inward', 'farm_inward.id', '=', 'farm_inward_details.pid')
            ->where('farm_inward_details.services', $ID)
           ->where('farm_inward.season',$season)
            ->whereDate('farm_inward.PurchaseDate', '<=', $Date)
           ->sum('farm_inward_details.Quantity');


             $petisum = DB::table('purchase_details')
             ->where('purchase_details.product', $ID)
            ->where('purchase_details.season',$season)
             ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
             ->sum('purchase_details.qty');



        $petires = $peticount-$petisum;

        return $petires;
    }

       public static function getNextInvoiceForFarmInward($location_id)
        {
            return DB::transaction(function () use ($location_id) {

                $location = DB::table('location')
                    ->where('id', $location_id)
                    ->first();

                // First 4 letters of location
                $prefix = strtoupper(substr($location->location, 0, 4));

                $last = DB::table('farm_inward')
                    ->where('location_id', $location_id)
                    ->lockForUpdate()
                    ->orderBy('invoice_no', 'desc')
                    ->first();

                $next = $last ? $last->invoice_no + 1 : 1;

                return [
                    'number' => $next,
                    'formatted' => 'FARMIN-' . $prefix . str_pad($next, 3, '0', STR_PAD_LEFT)
                ];
            });
        }

    public static function getNextBatchForFarmInward($location_id)
    {
        return DB::transaction(function () use ($location_id) {

            $location = DB::table('location')
                ->where('id', $location_id)
                ->first();

            // First 4 letters of location
            $prefix = strtoupper(substr($location->location, 0, 4));

            $last = DB::table('farm_inward')
                ->where('location_id', $location_id)
                ->lockForUpdate()
                ->orderBy('batch_no', 'desc')
                ->first();

            $next = $last ? $last->batch_no + 1 : 1;

            return [
                'number' => $next,
                'formatted' => 'BATCH-' . $prefix . str_pad($next, 3, '0', STR_PAD_LEFT),
            ];
        });
    }


      public static function getNextInvoiceForFarmDC($location_id)
        {
            return DB::transaction(function () use ($location_id) {

                $location = DB::table('location')
                    ->where('id', $location_id)
                    ->first();

                // First 4 letters of location
                $prefix = strtoupper(substr($location->location, 0, 4));

                $last = DB::table('farm_delivery_challan')
                    ->where('from_location_id', $location_id)
                    ->lockForUpdate()
                    ->orderBy('invoice_no', 'desc')
                    ->first();

                $next = $last ? $last->invoice_no + 1 : 1;

                return [
                    'number' => $next,
                    'formatted' => 'FARMDC-' . $prefix . str_pad($next, 3, '0', STR_PAD_LEFT)
                ];
            });
        }


public static function getFarmStock($service, $size, $stage, $batch_number)
{

    $inward = DB::table('farm_inward_details')
        ->where([
            'services'=>$service,
            'size'=>$size,
            'stage'=>$stage,
            'batch_number'=>$batch_number
        ])
        ->sum('Quantity');

    $outward = DB::table('farm_delivery_challan_details')
        ->where([
            'services'=>$service,
            'size'=>$size,
            'stage'=>$stage,
            'batch_number'=>$batch_number
        ])
        ->sum('Quantity');

    return max($inward - $outward, 0);
}


public static function getMainStock($location_id, $service, $size, $stage, $batch_number)
{
    $inward = DB::table('farm_inward_details')
        ->where([
            'location_id' => $location_id,
            'services' => $service,
            'size' => $size,
            'stage' => $stage,
            'batch_number' => $batch_number
        ])
        ->sum('Quantity');

    $outward = DB::table('farm_delivery_challan_details')
        ->where([
            'location_id' => $location_id,
            'services' => $service,
            'size' => $size,
            'stage' => $stage,
            'batch_number' => $batch_number
        ])
        ->sum('Quantity');

    return max($inward - $outward, 0);
}

}
