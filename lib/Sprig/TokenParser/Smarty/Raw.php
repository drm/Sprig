<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Raw extends Twig_TokenParser {
    function __construct($name, $dataNodeImpl) {
        $this->name = $name;
        $this->nodeImpl = $dataNodeImpl;
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return $this->name;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $data = $stream->expect(Twig_Token::TEXT_TYPE);
        $stream->expect(Twig_Token::BLOCK_START_TYPE);
        $stream->expect(Twig_Token::NAME_TYPE, 'end' . $this->getTag());
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $nodeImpl = $this->nodeImpl;
        return new $nodeImpl($data->getValue(), $data->getLine());
    }
}