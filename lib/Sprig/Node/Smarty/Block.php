<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Block extends Sprig_Node_Smarty
{
    function compile($compiler)
    {
        $compiler
                ->addDebugInfo($this)
                ->write('if(!isset($_block_params)) { $_block_params = array(); }' . "\n")
                ->write('array_push($_block_params, ' . "\n")
                ->indent()
                ->subcompile($this->getNode('parameters'))
                ->outdent()
                ->raw("\n")
                ->write(");\n")
                ->write('echo smarty_block_' . $this->tag)
                ->raw('($_block_params, null, $this);' . "\n")
                ->write('ob_start();' . "\n")
                ->subcompile($this->getNode('body'))
                ->write('echo smarty_block_' . $this->tag)
                ->raw('(array_pop($_block_params), ob_get_clean(), $this);' . "\n");
    }
}