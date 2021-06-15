<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller {
    // CREATE
    public function create(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }

    // READ
    public function read(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
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
