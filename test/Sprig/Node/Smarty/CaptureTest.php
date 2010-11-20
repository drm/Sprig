<?php
require_once dirname(__FILE__) . '/../AbstractTest.php';

class Sprig_Node_Smarty_CaptureTest extends Sprig_Node_AbstractTest
{
    /**
     * @covers Sprig_Node_Smarty_Capture::compile
     */
    function testCompile()
    {
        $value = rand(1000, 9999);
        $node = new Sprig_Node_Smarty_Capture('capture', array('assign' => $this->_expr('test')), new Twig_Node_Text($value, -1), null);
        $context =& $this->context;
        eval($this->compiler->compile($node)->getSource());
        $this->assertEquals($value, $this->context['test']);
    }

    /**
     * @covers Sprig_Node_Smarty_Capture
     * @expectedException Sprig_SyntaxError
     */
    function testNotSetVarThrowsSyntaxError()
    {
        $o = new Sprig_Node_Smarty_Capture('capture', array(), null, null);
        $o->compile($this->compiler);
    }

    /**
     * @covers Sprig_Node_Smarty_Capture
     * @expectedException Sprig_SyntaxError
     */
    function testInvalidTypeThrowsSyntaxError()
    {
        $o = new Sprig_Node_Smarty_Capture('capture', array('assign' => new Twig_Node_Expression_Name('test', null)), null, null);
        $o->compile($this->compiler);
    }
}