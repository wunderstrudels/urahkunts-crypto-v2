<?php
class Math {
    public static function percentageOf($original, $current) {
        $diff = $original - $current;
        return 100 - (($diff / $original) * 100);
    }

    public static function percentageFrom($original, $current) {
        $diff = $original - $current;
        return (($diff / $original) * 100) * -1;
    }
}

?>