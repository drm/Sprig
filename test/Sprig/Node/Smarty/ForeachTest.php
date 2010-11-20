<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

require_once dirname(__FILE__) . '/../../../assets/BodyStub.php';

class Sprig_Node_Smarty_ForeachTest extends Sprig_Node_AbstractTest
{
    function setUp()
    {
        parent::setUp();
        $this->node = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => $this->_expr('itemName'),
            ),
            new Sprig_Node_Expression_BodyStub('$stub[] = $context[\'itemName\'];'),
            new Sprig_Node_Expression_BodyStub('$stub = "else";'),
            null
        );
    }


    function testArray()
    {
        $this->context['array'] = array(1, 2, 3);

        $stub = array();
        $context = $this->context;
        eval($this->compiler->compile($this->node)->getSource());

        $this->assertEquals($this->context['array'], $stub);
    }


    function testElse()
    {
        $this->context['array'] = array();

        $stub = array();
        $context = $this->context;
        eval($this->compiler->compile($this->node)->getSource());

        $this->assertEquals("else", $stub);
    }

    function testKey()
    {
        $this->context['array'] = array('a' => 'b', 'c' => 'd');

        $this->node = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => $this->_expr('itemName'),
                 'key' => $this->_expr('keyName'),
            ),
            new Sprig_Node_Expression_BodyStub('$stub[] = $context[\'keyName\'] . $context[\'itemName\'];'),
            new Sprig_Node_Expression_BodyStub('$stub = "else";'),
            null
        );
        $stub = array();
        $context = $this->context;
        eval($this->compiler->compile($this->node)->getSource());

        $this->assertEquals(array('ab', 'cd'), $stub);
    }

    function testName()
    {
        $this->context['array'] = array(1);

        $this->node = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => $this->_expr('itemName'),
                 'name' => $this->_expr('name'),
            ),
            new Sprig_Node_Expression_BodyStub('$stub = $context[\'smarty\'][\'foreach\'];'),
            null,
            null
        );
        $stub = array();
        $context = $this->context;
        eval($this->compiler->compile($this->node)->getSource());

        $this->assertEquals(array('name' => array('total' => 1, 'iteration' => 1)), $stub);
    }


    /**
     * @expectedException Sprig_SyntaxError
     */
    function testItemIsRequired()
    {
        $o = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1)
            ),
            new Sprig_Node_Expression_BodyStub(''),
            new Sprig_Node_Expression_BodyStub(''),
            null
        );
        $o->compile($this->compiler);
    }


    /**
     * @expectedException Sprig_SyntaxError
     */
    function testItemMustBeConstantName()
    {
        $o = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => new Sprig_Node_Expression_Var('array', -1)
            ),
            new Sprig_Node_Expression_BodyStub(''),
            new Sprig_Node_Expression_BodyStub(''),
            null
        );
        $o->compile($this->compiler);
    }


    /**
     * @expectedException Sprig_SyntaxError
     */
    function testKeyMustBeConstantName()
    {
        $o = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => new Twig_Node_Expression_Constant('item', -1),
                 'key' => new Sprig_Node_Expression_Var('array', -1)
            ),
            new Sprig_Node_Expression_BodyStub(''),
            new Sprig_Node_Expression_BodyStub(''),
            null
        );
        $o->compile($this->compiler);
    }


    /**
     * @expectedException Sprig_SyntaxError
     */
    function testNameMustBeConstantName()
    {
        $o = new Sprig_Node_Smarty_Foreach(
            'foreach',
            array(
                 'from' => new Sprig_Node_Expression_Var('array', -1),
                 'item' => new Twig_Node_Expression_Constant('item', -1),
                 'key' => new Twig_Node_Expression_Constant('key', -1),
                 'name' => new Sprig_Node_Expression_Var('name', -1),
            ),
            new Sprig_Node_Expression_BodyStub(''),
            new Sprig_Node_Expression_BodyStub(''),
            null
        );
        $o->compile($this->compiler);
    }
}
