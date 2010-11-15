<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Block extends Sprig_Node_Smarty {
    function compile($compiler) {

        $compiler
                ->addDebugInfo($this)
                ->write('if(!isset($_block_params)) { $_block_params = array(); }' . "\n")
                ->write('array_push($_block_params, array(' . "\n")
                ->indent();
        ;

        $i = 0;
        foreach($this->attributes as $name => $expression) {
            if($i ++ > 0) {
                $compiler->raw(",\n");
            }
            $compiler
                    ->write("")->repr($name)->raw(' => ')
                    ->subcompile($expression)
            ;
        }
        $compiler
                ->outdent()
                ->raw("\n")
                ->write("));\n")
                ->write('smarty_block_' . $this->tagName)
                    ->raw('($_block_params, null, $this);' . "\n")
                ->write('ob_start();' . "\n")
                ->subcompile($this->body)
                ->write('smarty_block_' . $this->tagName)
                    ->raw('(array_pop($_block_params), ob_get_clean(), $this);' . "\n")
        ;
    }
}