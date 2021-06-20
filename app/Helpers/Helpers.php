<?php
class Helpers {

    public static function number($value, $decimals = 8){
        return number_format($value, $decimals);
    }
    
    private static function random_color_part($min, $max) {
        return str_pad(dechex(mt_rand($min, $max)), 2, '0', STR_PAD_LEFT);
    }
    
    public static function random_color() {
        return "#" . self::random_color_part(0, 255) . self::random_color_part(0, 255) . self::random_color_part(0, 255);
    }

    public static function bot_color() {
        return "#" . self::random_color_part(50, 200) . self::random_color_part(50, 200) . self::random_color_part(50, 50);
    }
}

?>