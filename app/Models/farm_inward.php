<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class farm_inward extends Model
{
    use HasFactory;

    protected $table = 'farm_inward';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'id',	
        'user_id',
        'updateduser',
        'PurchaseDate',
        'location_id',
        'location_name',
        'Tquantity',
        'season',
        'supplier',
        'supplier_id',
        'update_id',
        'batch_no',
        'invoice_no ',
        'Invoicenumber'
    ];


    public function details()
    {
        return $this->hasMany(farm_inward_details::class, 'pid', 'id');
    }

    
}
