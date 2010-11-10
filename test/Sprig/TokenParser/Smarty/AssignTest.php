<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */


class Sprig_TokenParser_Smarty_AssignTest extends Sprig_TokenParser_AbstractTest {
    function testSyntax() {
        $this->assertNodeType('Sprig_Node_Smarty_Assign', '{assign var=name value=x}');
    }
}
