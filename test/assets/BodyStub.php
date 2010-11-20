<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Expression_BodyStub extends Twig_Node
{
    function __construct($code)
    {
        $this->bodyCode = $code;
    }


    function compile($compiler)
    {
        $compiler->raw($this->bodyCode);
    }
}