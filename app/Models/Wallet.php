<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_id',
        'name',
        'amount',
        'timeout',
        'status',
    ];


    // Relations.
    public function currency() {
    	return $this->belongsTo("App\Models\Currency");
    }

    public function bots() {
        return $this->hasMany("App\Models\Bot");
    }

    public function transactions() {
        return $this->hasMany("App\Models\Transaction");
    }
}
