<?php
return function($active, $latest) {
    if($latest->value < 40000.00) {
        return false;
    }

    return true;
};
?>