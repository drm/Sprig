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
        } elseif($token->getType() == Twig_Token::NAME_TYPE) {
            $this->parser->getStream()->next();
            
            // test for a function call. Assume the function is a filter.
            // TODO implement a separate node for this. (or visitor?)
            if($this->parser->getStream()->test(Twig_Token::OPERATOR_TYPE, '(')) {
                $arguments = $this->parseArguments();
                if(count($arguments) > 0) {
                    $firstArg = $arguments->getNode(0);
                    $arguments->removeNode(0);
                    if(count($arguments) == 0) {
                        $arguments = null;
                    }
                } else {
                    throw new UnexpectedValueException("Unsupported situation: a function call without arguments can not be rewritten (yet)");
                    $firstArg = new Sprig_Node_Expression_Void();
                }
                $node = new Sprig_Node_Expression_Test($firstArg, $token->getValue(), $arguments, $token->getLine());
            } else {
                $node = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
                if(!$assignment) {
                    $node = $this->parsePostfixExpression($node);
                }
            }
        } else {
            $node = parent::parsePrimaryExpression($assignment);
        }

        return $node;
    }
    
    
    public function parseFilterExpressionRaw()
    {
        $filters = array();
        while (true) {
            $token = $this->parser->getStream()->expect(Twig_Token::NAME_TYPE);

            $filters[] = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
            if (!$this->parser->getStream()->test(Twig_Token::OPERATOR_TYPE, array(':', '('))) {
                $filters[] = new Twig_Node();
            } else {
                $filters[] = $this->parseFilterArguments();
            }

            if (!$this->parser->getStream()->test(Twig_Token::OPERATOR_TYPE, '|')) {
                break;
            }

            $this->parser->getStream()->next();
        }

        return new Twig_Node($filters);
    }
    
    
    
    public function parseFilterArguments() {
        $stream = $this->parser->getStream();
        
        if($stream->test(Twig_Token::OPERATOR_TYPE, '(')) {
            $ret = parent::parseArguments();
        } else {
            $stream->expect(Twig_Token::OPERATOR_TYPE, ':');
            $args = array();
            do {
                if(count($args)) {
                    $stream->expect(Twig_Token::OPERATOR_TYPE, ':');
                }
                $args[]= $this->parseExpression();
            } while($stream->test(Twig_Token::OPERATOR_TYPE, ':'));
            $ret = new Twig_Node($args);
        }
        return $ret;
    }
}
