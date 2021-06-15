<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Market extends Model
{
    use HasFactory;

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
        'saved'
    ];

    public function currency() {
    	return $this->belongsTo("App\Models\Currency");
    }

    public static function high_low($currency_id) {
    	return DB::table('markets_high_low')->where("currency_id", "=", $currency_id);
    }
}
