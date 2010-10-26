<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Foreach extends Sprig_Node_Smarty {
    public $else;

    
    function __construct($tagName, $attributes, $body, $else)
    {
        parent::__construct($tagName, $attributes, $body);
        $this->else = $else;
    }


    public function compile($compiler)
    {
        $compiler
            ->addDebugInfo($this)
        ;
        $itemName = $keyName = $nameName = null;

        if(!$this->hasAttribute('item')) {
            throw new Sprig_SyntaxError('Item attribute is required', null); // TODO line number
        } elseif($this->attributes['item'] instanceof Twig_Node_Expression_Constant) {
            $itemName = $this->attributes['item']->getAttribute('value');
        } else {
            throw new Sprig_SyntaxError('item must be a literal name', $this->attributes['item']->getLine());
        }
        if($this->hasAttribute('key')) {
            if(!$this->attributes['key'] instanceof Twig_Node_Expression_Constant) {
                throw new Sprig_SyntaxError('key must be a literal name', $this->attributes['key']->getLine());
            }
            $keyName = $this->attributes['key']->getAttribute('value');
        }
        if($this->hasAttribute('name')) {
            if(!$this->attributes['name'] instanceof Twig_Node_Expression_Constant) {
                throw new Sprig_SyntaxError('name must be a literal name', $this->attributes['name']->getLine());
            }
            $nameName = $this->attributes['name']->getAttribute('value');
        }

        $compiler
                ->write('$context[\'_parent\'] = (array) $context;'."\n")
                ->write('$from = (array)')
                ->subcompile($this->attributes['from'])
                ->raw(';' ."\n")
        ;
        if($this->else) {
            $compiler
                    ->write('if(empty($from) || count($from) == 0) {' . "\n")
                    ->indent()
                    ->subcompile($this->else)
                    ->outdent()
                    ->write("} else {\n")
                    ->indent();
        }


        if($nameName) {
            $magicContext = '$context[\'smarty\'][\'foreach\'][\'' . $nameName . '\']';
            $compiler->write($magicContext . '[\'total\'] = count($from);' . "\n");
            $compiler->write($magicContext . '[\'iteration\'] = 0;' . "\n");
        }

        $compiler
                ->write('foreach($from as '); 
        if($keyName) {
            $compiler->raw('$context[\'' . $keyName .'\'] => ');
        }
        $compiler->raw('$context[\'' . $itemName . '\']) {')
                ->indent()
        ;
        if($nameName) {
            $compiler->write($magicContext . '[\'iteration\'] ++;' . "\n");
        }

        $compiler
                ->subcompile($this->body)
                ->outdent()
                ->write('}');
        if($this->else) {
            $compiler
                    ->outdent()
                    ->write("}\n");
        }

        $compiler->write('$_parent = $context[\'_parent\'];'."\n");

        // remove some "private" loop variables (needed for nested loops)
//        $compiler->write('unset($context[\'_seq\'], $context[\'_iterated\'], $context[\''.$this->getNode('key_target')->getAttribute('name').'\'], $context[\''.$this->getNode('value_target')->getAttribute('name').'\'], $context[\'_parent\'], $context[\'loop\']);'."\n");

        /// keep the values set in the inner context for variables defined in the outer context
        $compiler->write('$context = array_merge($_parent, array_intersect_key($context, $_parent));'."\n");
    }
}