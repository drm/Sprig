<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Capture extends Sprig_Node_Smarty {
    public function compile($compiler)
    {
        $compiler->write('ob_start();');
        $compiler->subcompile($this->body);
        $compiler->write('$context[\'' . $this->attributes['assign']->getAttribute('value') . '\']= ob_get_clean();');
    }
}