<?php
return function($data, $latest) {
    $first = $data[0];
    $second = $data[1];
    $third = $data[2];


    $start = new Carbon\Carbon($latest->created_at, 'Europe/Copenhagen');
    //$past = \App\Models\Market::markets_saved()->where("currency_id", "=", $latest->currency_id)->where('created_at', '>=', $start->parse('-20 minutes'))->get();
    
    
    /* $fall = \Math::percentageFrom($past, $latest->value);

    
    if($fall > -0.05) {
        return false;   
    } */

    $start = $start->parse('-20 minutes');
    //$temp = $past[0]->created_at;
    Log::debug("latest: {$latest->created_at}");
    Log::debug("start: {$start}");
    //Log::debug("past: {$temp}");


    if($third >= $second || $second >= $first) {
        //return false;
    }

    return true;
};
?>