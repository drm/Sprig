<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_ConfigLoad extends Sprig_Node_Smarty
{
    function compile($compiler)
    {
        $compiler
                ->write('$_config_vars = $this->getEnvironment()->getConfig(')
                ->subcompile($this->getParameter('file'));
        if($this->hasParameter('section')) {
            $compiler
                    ->raw(', ')
                    ->subcompile($this->getParameter('section'))
            ;
        }
        $compiler
                ->raw(');')
                ->raw("\n")
                ->write('$context = array_merge($context, array(\'_config\' => $_config_vars));')
        ;
    }
}