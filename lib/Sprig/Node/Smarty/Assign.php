<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Assign extends Sprig_Node_Smarty {
    public function compile($compiler)
    {
//        $itemName = $this->attributes['item']->getAttribute('name');
        
        $compiler->write('$context[\'' . $this->attributes['var']->getAttribute('value') . '\'] = ');
        $compiler->subcompile($this->attributes['value']);
        $compiler->raw(';' . "\n");
    }
}