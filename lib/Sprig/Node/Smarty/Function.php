<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Function extends Sprig_Node_Smarty {
    function compile($compiler) {
        $compiler
                ->addDebugInfo($this)
                ->write('smarty_function_' . $this->tagName)
                ->raw("(\n")
                ->indent()
                ->write('array(' . "\n")
                ->indent()
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
        $compiler->outdent()->write("),\n")->write("\$this\n")->outdent()->write(");\n");;
    }
}