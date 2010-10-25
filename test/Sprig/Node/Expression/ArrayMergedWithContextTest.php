<?php

class Sprig_Node_Expression_ArrayMergedWithContextTest extends PHPUnit_Framework_TestCase {
    function testCompile() {
        $context = array('a' => 0, 'b' => 2);
        $compiler = new Twig_Compiler();
        
        $var = array(
            'a' => new Twig_Node_Expression_Constant(1, -1), 
            'c' => new Twig_Node_Expression_Constant('3', -1)
        );
        $node = new Sprig_Node_Expression_ArrayMergedWithContext($var, -1);
        $node->compile($compiler);
        eval('$actual = ' . $compiler->getSource() . ';');
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), $actual);
    }
}
