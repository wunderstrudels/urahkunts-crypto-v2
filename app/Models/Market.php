<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Market extends Model
{
    use HasFactory;
    protected $connection = 'market';
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_id',
        'value',
        'value_difference',
        'percent_difference',
    ];

    public static function markets_saved() {
        return DB::connection("market")->table('markets_saved');
    }

    
}
