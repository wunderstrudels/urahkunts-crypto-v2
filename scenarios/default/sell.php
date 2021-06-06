<?php
return function($data, $active, $latest) {
    $max = \App\Models\Market::markets_saved()->where("currency_id", "=", $latest->currency_id)->where('created_at', '>=', $latest->created_at)->max("value");
        
    if($latest->value >= $max) {
        $max = $latest->value;
    }


    if($latest->value < $active->buy_value) {
        return false;
    }  
    $diff = \Math::percentageFrom($active->buy_value, $latest->value);


    if($diff < 0.3) {
        return false;
    }


    
    $old = $max - $active->buy_value;
    $new = $latest->value - $max;
    $profit = \Math::percentageOf($old, $new);


    if($profit > -20 ) {
        return false;
    }

    return true;
};
?>