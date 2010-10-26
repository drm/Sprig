<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Capture extends Sprig_Node_Smarty {
    function __construct($tagName, $attributes, $body)
    {
        parent::__construct($tagName, $attributes, $body);

    }

    public function compile($compiler)
    {
        if(! $this->hasAttribute('assign') ) {
            throw new Sprig_SyntaxError('Missing assign attribute', $this->lineno);
        }
        if( !($this->attributes['assign'] instanceof Twig_Node_Expression_Constant) ) {
            throw new Sprig_SyntaxError('Need constant assign attribute', $this->lineno);
        }
        $compiler->write('ob_start();');
        $compiler->subcompile($this->body);
        $compiler->write('$context[\'' . $this->attributes['assign']->getAttribute('value') . '\']= ob_get_clean();');
    }
}