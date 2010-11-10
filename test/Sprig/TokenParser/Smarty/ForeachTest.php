<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_ForeachTest extends Sprig_TokenParser_AbstractTest {
    function testSyntax() {
        $this->assertNodeType('Sprig_Node_Smarty_Foreach', '{foreach from=x item=y} {/foreach}');
        $this->assertNodeType('Sprig_Node_Smarty_Foreach', '{foreach from=x item=y} {foreachelse} {/foreach}');
    }

    /**
     * @expectedException Twig_Error_Syntax
     */
    function testUnclosedBlockThrowsException() {
        $this->assertNodeType('', '{foreach from=x item=y}');
    }
}
