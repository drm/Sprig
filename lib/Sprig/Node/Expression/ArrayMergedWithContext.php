<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_Node_Expression_ArrayMergedWithContext extends Twig_Node_Expression_Array
{
    public function compile($compiler)
    {
        $compiler->raw('array_merge($context, ');
        parent::compile($compiler);
        $compiler->raw(')');
    }

}