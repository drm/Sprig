<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Foreach extends Sprig_Node_Smarty
{
    function __construct($tagName, $parameters, $body, $else, $lineNo)
    {
        parent::__construct($tagName, $parameters, $body, $lineNo);
        $this->setNode('else', $else);
        $this->else = $else;
    }


    public function compile($compiler)
    {
        $compiler
                ->addDebugInfo($this);
        $itemName = $keyName = $nameName = null;

        if (!$this->hasParameter('item')) {
            throw new Sprig_SyntaxError('Item attribute is required', null); // TODO line number
        } elseif ($this->getParameter('item') instanceof Twig_Node_Expression_Constant) {
            $itemName = $this->getParameter('item')->getAttribute('value');
        } else {
            throw new Sprig_SyntaxError('item must be a literal name', $this->getParameter('item')->getLine());
        }
        if ($this->hasParameter('key')) {
            if (!$this->getParameter('key') instanceof Twig_Node_Expression_Constant) {
                throw new Sprig_SyntaxError('key must be a literal name', $this->getParameter('item')->getLine());
            }
            $keyName = $this->getParameter('key')->getAttribute('value');
        }
        if ($this->hasParameter('name')) {
            if (!$this->getParameter('name') instanceof Twig_Node_Expression_Constant) {
                throw new Sprig_SyntaxError('name must be a literal name', $this->getParameter('name')->getLine());
            }
            $nameName = $this->getParameter('name')->getAttribute('value');
        }

        $compiler
                ->write('$context[\'_parent\'] = (array) $context;' . "\n")
                ->write('$from = (array)')
                ->subcompile($this->getParameter('from'))
                ->raw(';' . "\n");
        if ($this->else) {
            $compiler
                    ->write('if(empty($from) || count($from) == 0) {' . "\n")
                    ->indent()
                    ->subcompile($this->getNode('else'))
                    ->outdent()
                    ->write("} else {\n")
                    ->indent();
        }


        if ($nameName) {
            $magicContext = '$context[\'smarty\'][\'foreach\'][\'' . $nameName . '\']';
            $compiler->write($magicContext . '[\'total\'] = count($from);' . "\n");
            $compiler->write($magicContext . '[\'iteration\'] = 0;' . "\n");
        }

        $compiler
                ->write('foreach($from as ');
        if ($keyName) {
            $compiler->raw('$context[\'' . $keyName . '\'] => ');
        }
        $compiler->raw('$context[\'' . $itemName . '\']) {')
                ->indent();
        if ($nameName) {
            $compiler->write($magicContext . '[\'iteration\'] ++;' . "\n");
        }

        $compiler
                ->subcompile($this->getNode('body'))
                ->outdent()
                ->write('}');
        if ($this->else) {
            $compiler
                    ->outdent()
                    ->write("}\n");
        }

        $compiler->write('$_parent = $context[\'_parent\'];' . "\n");

        // remove some "private" loop variables (needed for nested loops)
        //        $compiler->write('unset($context[\'_seq\'], $context[\'_iterated\'], $context[\''.$this->getNode('key_target')->getAttribute('name').'\'], $context[\''.$this->getNode('value_target')->getAttribute('name').'\'], $context[\'_parent\'], $context[\'loop\']);'."\n");

        /// keep the values set in the inner context for variables defined in the outer context
        $compiler->write('$context = array_merge($_parent, array_intersect_key($context, $_parent));' . "\n");
    }
}