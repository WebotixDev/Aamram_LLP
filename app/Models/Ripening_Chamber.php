<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ripening_Chamber extends Model
{
    use HasFactory;

    protected $table = 'ripening_chamber';


    protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'billdate',
        'receive_location_id',
        'receive_location_name',
        'warehouse_inward_No',
        'update_id',
        'invoice_no ',
        'Invoicenumber',
        'season',
    ];


    public function details()
    {
        return $this->hasMany(Ripening_Chamber_details::class, 'pid', 'id');
    }


}
