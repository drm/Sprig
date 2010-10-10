<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_TokenParser_Smarty_Foreach extends Sprig_TokenParser_SmartyBlock {
    public function parse(Twig_Token $token)
    {
        $this->tagName = $token->getValue();
        $this->parseAttributes();
        $body = $this->parser->subparse(array($this, 'decideForFork'));
        if ($this->parser->getStream()->next()->getValue() == 'foreachelse') {
            $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse(array($this, 'decideForEnd'), true);
        } else {
            $else = null;
        }
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        return new Sprig_Node_Smarty_Foreach($this->tagName, $this->attributes, $body, $else);
    }

    public function decideForFork($token)
    {
        return $token->test(array('endforeach', 'foreachelse'));
    }


    public function decideForEnd($token) {
        return $token->test(array('endforeach'));
    }


    function getTag() {
        return 'foreach';
    }
}