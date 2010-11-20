<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Php extends Twig_Node_Text {
    public function compile($compiler)
    {
        $compiler->write($this->getAttribute('data'));
    }
    
}