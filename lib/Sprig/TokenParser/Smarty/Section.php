<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Section extends Sprig_TokenParser_Smarty_Base {
    public function parse(Twig_Token $token)
    {
        $tagName = $token->getValue();
        $attributes = $this->parseAttributes();
        $body = $this->parser->subparse(array($this, 'decideForFork'));
        if ($this->parser->getStream()->next()->getValue() == 'sectionelse') {
            $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse(array($this, 'decideForEnd'), true);
        } else {
            $else = null;
        }
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        return new Sprig_Node_Smarty_Section($tagName, $attributes, $body, $else, $token->getLine());
    }


    public function decideForFork($token)
    {
        return $token->test(array('endsection', 'sectionelse'));
    }


    public function decideForEnd($token) {
        return $token->test(array('endsection'));
    }


    function getTag() {
        return 'section';
    }
}
