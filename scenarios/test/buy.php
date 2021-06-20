<?php
return function($current) {
    $now = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');

    // Daily average.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $daily = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(1, 'day'))->where("created_at", "<=", $now)->avg("value");
    if($current->value > $daily) {
        return "Current value is above daily average.";
    }


    // Rise in last 2min.
    $then = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $test = $this->current["currency"]->market()->where('created_at', '>=', $then->subtract(2, 'minutes'))->where("created_at", "<=", $now)->get();
    if($current->value <= $test[0] || $current->value <= $test[1]) {
        return "Waiting for upwards trend.";
    }



    return true;
};
?>