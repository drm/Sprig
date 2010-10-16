<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

        
abstract class Sprig_TokenParser_Smarty_Base implements Twig_TokenParserInterface {
    protected $parser;

    final public function parseAttributes()
    {
        while($this->parser->getStream()->test(Twig_Token::NAME_TYPE)) {
            $attrName = $this->parser->getStream()->next()->getValue();
            $this->parser->getStream()->expect(Twig_Token::OPERATOR_TYPE, '=');
            $attributes[$attrName] = $this->parser->getExpressionParser()->parseExpression();
        }
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        return $attributes;
    }

    final public function setParser(Twig_Parser $parser)
    {
        $this->parser = $parser;
    }
}

