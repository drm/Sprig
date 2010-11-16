<?php

function smarty_modifier_str_replace($in, $a, $b) {
    return str_replace($a, $b, $in);
}