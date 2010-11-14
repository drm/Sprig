<?php

// entirely useless modifier, only used for testing.
function smarty_modifier_test_modifier($value, $prefix = '', $suffix = '') {
    $ret = strrev($prefix . $value . $suffix);
}
