<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Assign extends Sprig_Node_Smarty {
    function __construct($tagName, $attributes, $body)
    {
        parent::__construct($tagName, $attributes, $body);
    }



    public function compile($compiler)
    {
        if(! $this->hasAttribute('var')) {
            throw new Sprig_SyntaxError('Missing var attribute', $this->lineno);
        }
        if(! $this->attributes['var'] instanceof Twig_Node_Expression_Constant) {
            throw new Sprig_SyntaxError('Invalid argument for \'var\', need constant name', $this->lineno);
        }

        $compiler->write('$context[\'' . $this->attributes['var']->getAttribute('value') . '\'] = ');
        $compiler->subcompile($this->attributes['value']);
        $compiler->raw(';' . "\n");
    }
}