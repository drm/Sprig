<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Include extends Sprig_TokenParser_SmartyBlock {
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $this->tagName = $token->getValue();
        $this->parseAttributes();
        return new Twig_Node_Include($this->attributes['file'], null, $token->getLine());
    }


    function getTag() {
        return 'include';
    }
}