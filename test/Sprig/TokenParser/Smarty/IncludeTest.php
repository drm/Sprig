<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_IncludeTest extends Sprig_TokenParser_AbstractTest
{
    function testSyntax()
    {
        $this->assertNodeType('Twig_Node_Include', '{include file="x"}');
        $this->assertNodeType('Twig_Node_Include', '{include file="x" var1=x var2=y}');
    }
}