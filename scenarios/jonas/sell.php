<?php
return function($active, $current) {
    $now = new \Carbon\Carbon($current->created_at, 'Europe/Copenhagen');
    $max = $this->current["currency"]->market()->where('created_at', '>=', $active->created_at)->where("created_at", "<=", $now)->max("value");
    if($current->value >= $max) {
        $max = $current->value;
    }

    $diff = \Math::percentageFrom($active->buy_value, $current->value);
    if($diff < 0.25) {
        return "Waiting for profit current: " . number_format($diff, 2, '.', '') . "%";
    }


    $old = $max - $active->buy_value;
    $new = $current->value - $max;
    $profit = \Math::percentageOf($old, $new);
    if($profit > -10) {
        return "Waiting to sell: " . number_format($profit, 2, '.', '') . "% diff: " . number_format($diff, 2, '.', '') . "%";
    }

    if($current->value < $active->buy_value) {
        return "Waiting for profit. Currently bolow buy value.";
    }
    Log::debug("Profit: {$diff}     Profit-fall: {$profit}      --      Time: {$current->created_at}");

    return true;
};
?>