<?php
return function($current) {
    $now = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    
    // Daily average.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $daily = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(1, 'day'))->where("created_at", "<=", $now)->avg("value");
    if($current->value > $daily) {
        return "Current value is above daily average.";
    }

    $last = $this->current["wallet"]->transactions()->where("status", "=", "selling")->latest()->first();
    if($last != null) {
        $diff = \Math::percentageFrom($last->buy_value, $current->value);
        if($diff > -0.6) {
            return "Still too close to last buy: " . number_format($diff, 2, '.', '') . "%";
        }
    }

    // Max value in 30min.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $max = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(10, 'minutes'))->where("created_at", "<=", $now)->max("value");
    $max = ($max < $current->value) ? $current->value : $max;

    $fall = \Math::percentageFrom($max, $current->value);
    if($fall > -0.2) {
        return "Waiting for downwards trend. Current: " . number_format($fall, 2, '.', '') . "%";   
    }


    // Rise.
    if($current->percent_difference < 0.01) {
        return "Waiting for upwards trend. Current difference: " . number_format($current->percent_difference, 2, '.', '');
    }
    Log::debug("Fall: {$fall}  Diff: {$current->percent_difference}       --      Time: {$current->created_at}");

    return true;
};
?>