<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Capture extends Sprig_Node_Smarty {
    public function compile($compiler)
    {
        if(! $this->hasParameter('assign') ) {
            throw new Sprig_SyntaxError('Missing assign attribute', $this->lineno);
        }
        if( !($this->getParameter('assign') instanceof Twig_Node_Expression_Constant) ) {
            throw new Sprig_SyntaxError('Need constant assign attribute', $this->lineno);
        }
        $compiler->write('ob_start();');
        $compiler->subcompile($this->getNode('body'));
        $compiler->write('$context[')->subcompile($this->getParameter('assign'))->raw(']= ob_get_clean();');
    }
}