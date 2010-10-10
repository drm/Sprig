<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_ExpressionParser extends Twig_ExpressionParser
{
    public function parsePrimaryExpression($assignment = false)
    {
        $token = $this->parser->getCurrentToken();
        if($token->getType() == Sprig_Token::VAR_TYPE) {
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