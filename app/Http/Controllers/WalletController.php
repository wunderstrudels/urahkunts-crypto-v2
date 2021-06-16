<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Wallet;

class WalletController extends Controller {
    // CREATE
    public function create(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }

    // READ
    public function read(Request $request, $id = null) {
        
        if($id == null) {
            return response()->json(Wallet::all(), 200);
        }

        $wallet = Wallet::where("id", "=", $id)->orWhere("name", "=", $id)->first();

        return response()->json($wallet, 200);
    }

    // UPDATE
    public function update(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }

    // DELETE
    public function delete(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }
}
