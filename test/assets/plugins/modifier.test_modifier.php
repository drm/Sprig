<?php

// entirely useless modifier, only used for testing.
function smarty_modifier_test_modifier($value, $prefix = '', $suffix = '') {
    return strrev($prefix . $value . $suffix);
}
