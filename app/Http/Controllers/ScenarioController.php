<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\Scenario;

class ScenarioController extends Controller {
    // CREATE
    public function create(Request $request) {
        
        return response()->json(array(
            "success" => true
        ));
    }

    // READ
    public function read(Request $request, $id = null) {
        if($id == null) {
            return response()->json(Scenario::all());
        }

        return response()->json(Scenario::where("id", "=", $id)->orWhere("name", "=", $id)->first());
    }

    

    // UPDATE
    public function update(Request $request, $id) {
        
       /*  $scenario = Scenario::where("id", "=", $id)->orWhere("name", "=", $id)->first();

        $scenario->status = null;
        $scenario->save();

        Artisan::call('command:trainer'); */

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












    // GRAPH
    public function graph(Request $request, $id) {
        
        $scenario = Scenario::where("id", "=", $id)->orWhere("name", "=", $id)->first();

        if($scenario == null) {
            return response()->json(array(
                "error" => "No scenario with name/id: " . $id
            ));
        }

        if(file_exists("../scenarios/" . $scenario->name . "/data.json") == false) {
            return response()->json(array(
                "training" => true
            ));
        }
        $data = "../scenarios/" . $scenario->name . "/data.json";
        $buy = "../scenarios/" . $scenario->name . "/buy.php";
        $sell = "../scenarios/" . $scenario->name . "/sell.php";

        return response()->json(array(
            "graph" => json_decode(file_get_contents($data)),
            "buy" => file_get_contents($buy),
            "sell" => file_get_contents($sell)
        ));
    }





    // GRAPH
    public function code(Request $request, $id) {
        $scenario = Scenario::where("id", "=", $id)->orWhere("name", "=", $id)->first();

        if($scenario == null) {
            return response()->json(array(
                "error" => "No scenario with name/id: " . $id
            ));
        }

        $data = "../scenarios/" . $scenario->name . "/data.json";
        $buy = "../scenarios/" . $scenario->name . "/buy.php";
        $sell = "../scenarios/" . $scenario->name . "/sell.php";

        file_put_contents($buy, $request->buy);
        file_put_contents($sell, $request->sell);
        unlink($data);
        
        $scenario->status = null;
        $scenario->save();


        return response()->json(array(
            "success" => true
        ));
    }
}
