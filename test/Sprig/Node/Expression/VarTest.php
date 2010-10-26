<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */
require_once dirname(__FILE__) . '/../AbstractTest.php';

class Sprig_Node_Expression_VarTest extends Sprig_Node_AbstractTest {
    function testCompile() {
        $this->assertExpressionResultEquals('2', new Sprig_Node_Expression_Var('b', -1));
    }
}