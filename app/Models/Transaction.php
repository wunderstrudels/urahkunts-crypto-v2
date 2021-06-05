<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'scenario_id',
        'amount',
        'buy_id',
        'buy_value',
        'buy_price',
        'sell_id',
        'sell_value',
        'sell_price',
        'sold_at',
        'status'
    ];




    public static function active($scenario) {
        return Transaction::where(array(
            "scenario_id" => $scenario->id,
            "status" => "selling"
        ))->first();
    }





    // Relations.
    public function wallet() {
    	return $this->belongsTo("App\Models\Wallet");
    }

    public function scenario() {
    	return $this->belongsTo("App\Models\Scenario");
    }
}
