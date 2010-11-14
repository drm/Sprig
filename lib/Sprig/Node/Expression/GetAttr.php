<?php

class Sprig_Node_Expression_GetAttr extends Twig_Node_Expression_GetAttr
{
    protected $isCompileAsStrict = false;
    
       
    
    public function setCompileAsStrict($strict) 
    {
        $this->isCompileAsStrict = (bool) $strict;
    }
    
    
    public function isCompileAsStrict() 
    {
        return $this->isCompileAsStrict;
    }
    
    
    public function compile($compiler)
    {
        if(!$this->isCompileAsStrict()) {
            parent::compile($compiler);
        } elseif($this->getAttribute('type') != self::TYPE_ARRAY) {
            throw new UnexpectedValueException("Strict compilation for types other than ARRAY is not supported");
        } else {
            $compiler
                ->subcompile($this->getNode('node'))
                ->raw('[')
                ->subcompile($this->getNode('attribute'))
                ->raw(']');
        }
    }
}
