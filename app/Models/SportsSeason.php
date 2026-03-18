<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SportsSeason extends Model{
    use HasFactory;
    protected $fillable = [
        'sports_season_name',
        'sports_season_from',
        'sports_season_to',
        'sports_season_status',
        'sports_season_addedby',
        'sports_season_updatedby',
    ];

}