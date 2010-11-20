<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Expression_Var extends Twig_Node_Expression_Name
{
    protected $compileAsStrict = false;

    function setCompileAsStrict($strict)
    {
        $this->compileAsStrict = (bool)$strict;
    }


    function isCompileAsStrict()
    {
        return $this->compileAsStrict;
    }


    function compile($compiler)
    {
        if (!$this->isCompileAsStrict()) {
            parent::compile($compiler);
        } else {
            $compiler->raw(sprintf('$context[\'%s\']', $this->getAttribute('name')));
        }
    }
}
