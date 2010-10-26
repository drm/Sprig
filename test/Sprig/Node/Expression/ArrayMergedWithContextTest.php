<?php

require_once dirname(__FILE__) . '/../AbstractTest.php';

class Sprig_Node_Expression_ArrayMergedWithContextTest extends Sprig_Node_AbstractTest  {
    /**
     * @covers Sprig_Node_Expression_ArrayMergedWithContext
     */
    function testCompile() {
        $this->assertExpressionResultEquals(array('a' => 1, 'b' => 2, 'c' => 3), new Sprig_Node_Expression_ArrayMergedWithContext($this->_expr(array('a' => 1, 'c' => 3)), -1));
    }
}
