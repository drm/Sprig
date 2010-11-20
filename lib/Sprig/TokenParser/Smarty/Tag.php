<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */


abstract class Sprig_TokenParser_Smarty_Tag extends Sprig_TokenParser_Smarty_Base
{
    public $tagName = null, $attributes;


    public function parse(Twig_Token $token)
    {
        $class = $this->getNodeImpl();
        return new $class($token->getValue(), $this->parseAttributes(), null, $token->getLine());
    }


    abstract function getNodeImpl();
}