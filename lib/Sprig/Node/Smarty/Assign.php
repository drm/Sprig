<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Assign extends Sprig_Node_Smarty
{
    public function compile($compiler)
    {
        if (!$this->hasParameter('var')) {
            throw new Sprig_SyntaxError('Missing var attribute', $this->lineno);
        }
        if (!$this->getParameter('var') instanceof Twig_Node_Expression_Constant) {
            throw new Sprig_SyntaxError('Invalid argument for \'var\', need constant name', $this->lineno);
        }

        $compiler->write('$context[')->subcompile($this->getParameter('var'))->raw('] = ');
        $compiler->subcompile($this->getParameter('value'));
        $compiler->raw(';' . "\n");
    }
}