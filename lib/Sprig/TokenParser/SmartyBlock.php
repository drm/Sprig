<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

abstract class Sprig_TokenParser_SmartyBlock implements Twig_TokenParserInterface {
    protected $parser;
    public $tagName = null, $attributes;


    public function parseAttributes() {
        while($this->parser->getStream()->test(Twig_Token::NAME_TYPE)) {
            $attrName = $this->parser->getStream()->next()->getValue();
            $this->parser->getStream()->expect(Twig_Token::OPERATOR_TYPE, '=');
            $this->attributes[$attrName] = $this->parser->getExpressionParser()->parseExpression();
        }
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
    }
    
    public function setParser(Twig_Parser $parser)
    {
        $this->parser = $parser;// TODO: Implement setParser() method.
    }
    
}