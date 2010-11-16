<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Twig_Parser_Mock extends Twig_Parser {
    function testParse($stream) {
        // tag handlers
        $this->handlers = $this->env->getTokenParsers();
        $this->handlers->setParser($this);

        // node visitors
        $this->visitors = $this->env->getNodeVisitors();
        $this->expressionParser = new Twig_ExpressionParser($this);

        $this->stream = $stream;
        return $this->subparse(null);
    }
}

/**
 * @property Sprig_Lexer $lexer
 * @property Twig_Parser $parser
 */
abstract class Sprig_TokenParser_AbstractTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $env = new Sprig_Environment();
        $env->addExtension(new Sprig_Extension_Smarty());
        $this->parser = new Twig_Parser_Mock($env);
        $this->lexer = new Sprig_Lexer();
    }


    function assertNodeType($type, $code, $nodeIndex = 0) {
        $stream = $this->lexer->tokenize($code);
        $this->assertType($type, $this->parser->testParse($stream)->getNode($nodeIndex)); 
    }
}
