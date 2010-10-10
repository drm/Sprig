<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_Node_Smarty_Function extends Sprig_Node_Smarty {
    function compile($compiler) {
        $compiler
                ->addDebugInfo($this)
                ->write('smarty_function_' . $this->tagName)
                ->raw('($this, array(')
                ->indent()
        ;

        foreach($this->attributes as $name => $expression) {
            $compiler
                    ->write($name . ' => ')
                    ->subcompile($expression)
            ;
        }
        $compiler->outdent()->raw(');');
    }
}