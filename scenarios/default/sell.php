<?php
return function($data, $active, $latest) {
    if($latest->value > (35950.58 * 0.02)) {
        return false;
    }
    return true;
};
?>