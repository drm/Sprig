<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_SectionTest extends Sprig_TokenParser_AbstractTest {
    function testSyntax() {
        $this->assertNodeType('Sprig_Node_Smarty_Section', '{section loop=x}  {/section}');
        $this->assertNodeType('Sprig_Node_Smarty_Section', '{section loop=x} {sectionelse} {/section}');
    }

    /**
     * @expectedException Twig_Error_Syntax
     */
    function testUnclosedBlockThrowsException() {
        $this->assertNodeType('', '{section loop=x}');
    }
}
