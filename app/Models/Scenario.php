<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'name',
        'color',
        'status',
    ];


    // Relations.
    public function wallet() {
    	return $this->belongsTo("App\Models\Wallet");
    }
    
    public function transactions() {
    	return $this->hasMany("App\Models\Transaction");
    }
}
