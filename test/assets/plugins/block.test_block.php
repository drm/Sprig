<?php

function smarty_block_test_block($params, $contents, $smarty)
{
    if (is_null($contents)) {
        return;
    }

    echo str_replace($params['param1'], $params['param2'], $contents);
}