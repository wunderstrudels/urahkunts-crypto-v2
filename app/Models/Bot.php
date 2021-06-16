<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'scenario_id',
        'name',
        'color',
        'status',
    ];


    public function active() {
    	return $this->transactions->where("status", "!=", "sold")->first();
    }
    


    // Relations.
    public function wallet() {
    	return $this->belongsTo("App\Models\Wallet");
    }

    public function scenario() {
    	return $this->belongsTo("App\Models\Scenario");
    }
    
    public function transactions() {
    	return $this->hasMany("App\Models\Transaction");
    }
}
