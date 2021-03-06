<?php
return function($current) {
    return false;
    $now = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    
    // Daily average.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $daily = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(1, 'day'))->where("created_at", "<=", $now)->avg("value");
    if($current->value > $daily) {
        return "Current value is above daily average.";
    }

    


    // Max value in 30min.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $max = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(20, 'minutes'))->where("created_at", "<=", $now)->max("value");
    $max = ($max < $current->value) ? $current->value : $max;

    $fall = \Math::percentageFrom($max, $current->value);
    if($fall > -0.4) {
        return "Waiting for downwards trend. Current: " . number_format($fall, 2, '.', '') . "%";   
    }


    // Rise in last 2min.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $avg = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(1, 'minutes'))->where("created_at", "<=", $now)->avg("value");
    if($current->value <= $avg) {
        return "Waiting for upwards trend. Current average: " . number_format($avg, 2, '.', '');
    }

    return true;
};
?>