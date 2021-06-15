<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Currency;

class CurrencyController extends Controller {
    // READ
    public function read(Request $request, $short = null) {
        
        if($short == null) {
            return response()->json(Currency::all(), 200);
        }

        return response()->json(Currency::where("short", "=", $short)->first(), 200);
    }
}
