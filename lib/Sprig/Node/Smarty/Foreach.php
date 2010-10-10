<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Foreach extends Sprig_Node_Smarty {
    public function compile($compiler)
    {
        $compiler
            ->addDebugInfo($this)
        ;
        $itemName = null;
        $keyName = null;

        if($this->attributes['item'] instanceof Twig_Node_Expression_Name) {
            $itemName = $this->attributes['item']->getAttribute('name');
        }
        if($this->hasAttribute('key') && $this->attributes['key'] instanceof Twig_Node_Expression_Name) {
            $keyName = $this->attributes['key']->getAttribute('name');
        }
        
        $compiler
                ->write('foreach(')
                ->subcompile($this->attributes['from'])
                ->raw(' as '); 
        ;
        if($keyName) {
            $compiler->raw('$context[\'' . $keyName .'\'] => ');
        }
        $compiler->raw('$context[\'' . $itemName . '\']) {')
                ->indent()
        ;
        $compiler
                ->subcompile($this->body)
                ->outdent()
                ->write('}');
    }
}