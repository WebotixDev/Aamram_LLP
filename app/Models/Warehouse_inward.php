<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse_inward extends Model
{
    use HasFactory;

    protected $table = 'warehouse_inward';


    protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'billdate',
        'receive_location_id',
        'receive_location_name',
        'farm_dcNo',
        'update_id',
        'invoice_no ',
        'Invoicenumber',
        'season',
    ];


    public function details()
    {
        return $this->hasMany(Warehouse_inward_details::class, 'pid', 'id');
    }


}
