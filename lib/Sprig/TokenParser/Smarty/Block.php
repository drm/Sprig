<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

abstract class Sprig_TokenParser_Smarty_Block extends Sprig_TokenParser_Smarty_Tag
{
    public function parse(Twig_Token $token)
    {
        $class = $this->getNodeImpl();
        $attributes = $this->parseAttributes();
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        return new $class($token->getValue(), $attributes, $body, $token->getLine());
    }


    final function decideBlockEnd(Twig_Token $token)
    {
        return $token->test(array('end' . $this->getTag()));
    }
}