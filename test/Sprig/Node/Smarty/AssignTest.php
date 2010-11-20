<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */
require_once dirname(__FILE__) . '/../AbstractTest.php';

class Sprig_Node_AssignTest extends Sprig_Node_AbstractTest {
    function testCompile() {
        $value = rand(1000, 9999);
        $node = new Sprig_Node_Smarty_Assign('assign', array('var' => $this->_expr('test'), 'value' => $this->_expr($value)), null, null);
        $context =& $this->context;
        eval($this->compiler->compile($node)->getSource());
        $this->assertEquals($value, $this->context['test']);
    }


    /**
     * @expectedException Sprig_SyntaxError
     */
    function testNotSetVarThrowsSyntaxError() {
        $o = new Sprig_Node_Smarty_Assign('assign', array(), null, null);
        $o->compile($this->compiler);
    }

    /**
     * @expectedException Sprig_SyntaxError
     */
    function testInvalidTypeThrowsSyntaxError() {
        $o = new Sprig_Node_Smarty_Assign('assign', array('var' => new Twig_Node_Expression_Name('test', null)), null, null);
        $o->compile($this->compiler);
    }
}