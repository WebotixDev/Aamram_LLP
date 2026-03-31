<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooling_Chamber extends Model
{
    use HasFactory;

    protected $table = 'cooling_chamber';


    protected $fillable = [
        'id',
        'user_id',
        'updateduser',
        'billdate',
        'receive_location_id',
        'receive_location_name',
        'ripening_chamber_No',
        'update_id',
        'invoice_no ',
        'Invoicenumber',
        'season',
    ];


    public function details()
    {
        return $this->hasMany(Cooling_Chamber_details::class, 'pid', 'id');
    }


}
