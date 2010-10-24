<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_ExpressionParser extends Twig_ExpressionParser
{
    public function parsePrimaryExpression($assignment = false)
    {
        $token = $this->parser->getCurrentToken();
        if($token->getType() == Sprig_Token::CONFIG_TYPE) {
            $this->parser->getStream()->next();

            $node = new Twig_Node_Expression_GetAttr(
                new Sprig_Node_Expression_Var('_config', $token->getLine()),
                new Twig_Node_Expression_Constant($token->getValue(), $token->getLine()),
                new Twig_Node(),
                Twig_Node_Expression_GetAttr::TYPE_ARRAY,
                $token->getLine()
            );

            if (!$assignment) {
                $node = $this->parsePostfixExpression($node);
            }
        } elseif($token->getType() == Sprig_Token::VAR_TYPE) {
            $this->parser->getStream()->next();
            $node = new Sprig_Node_Expression_Var($token->getValue(), $token->getLine());

            if (!$assignment) {
                $node = $this->parsePostfixExpression($node);
            }
        } else {
            $node = parent::parsePrimaryExpression($assignment);
        }

        return $node;
    }

}