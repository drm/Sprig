<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Function extends Sprig_Node_Smarty
{
    function compile($compiler)
    {
        $compiler
                ->addDebugInfo($this)
                ->write('smarty_function_' . $this->getAttribute('tag'))
                ->raw("(\n")
                ->subcompile($this->getNode('parameters'))
                ->write(",\n")
                ->write("\$this\n")
                ->write(");\n");
    }
}