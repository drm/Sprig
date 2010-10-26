<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_CaptureTest extends Sprig_TokenParser_AbstractTest {
    function testSyntax() {
        $this->assertNodeType('Sprig_Node_Smarty_Capture', '{capture assign=piet} some value {/capture}');
    }

    /**
     * @expectedException Twig_SyntaxError
     */
    function testUnclosedBlockThrowsException() {
        $this->assertNodeType('Sprig_Node_Smarty_Capture', '{capture assign=piet}');
    }
}