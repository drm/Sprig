<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Include extends Sprig_TokenParser_Smarty_Base {
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $attributes = $this->parseAttributes();
        $vars = $attributes;
        unset($vars['file']);
        if(count($vars)) {
            $first = current($vars);
            $lineno = $first->getLine();
            $variables = new Sprig_Node_Expression_ArrayMergedWithContext($vars, $lineno);
        } else {
            $variables = null;
        }
        return new Twig_Node_Include($attributes['file'], $variables, false, $token->getLine());
    }


    function getTag() {
        return 'include';
    }
}
