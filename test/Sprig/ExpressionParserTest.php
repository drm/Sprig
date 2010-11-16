<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */
class Sprig_ExpressionParserTest_Parser extends Twig_Parser {
    function setStream($stream) {
        $this->stream = $stream;
    }
}

class Sprig_ExpressionParserTest_TokenStream extends Twig_TokenStream {

    public function next($fromStack = true)
    {
        try {
            return parent::next($fromStack);
        } catch(Exception $e){
            return null;
        }
    }


}

class Sprig_ExpressionParserTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->parser = new Sprig_ExpressionParserTest_Parser();
        $this->expressionParser = new Sprig_ExpressionParser($this->parser);
    }


    /**
     * @dataProvider expressions
     * @param  $expectedNodeXml
     * @param  $tokens
     * @return void
     */
    function testExpectedExpressionResult($expectedNodeXml, $tokens)
    {
        $this->parser->setStream(new Sprig_ExpressionParserTest_TokenStream($tokens, null));
        $this->assertEquals($expectedNodeXml, $this->expressionParser->parsePrimaryExpression()->toXml());
    }


    function expressions() {
        return array(
//            array(
//                '<a></a>',
//                array(
//                    new Twig_Token(Twig_Token::STRING_TYPE, "a", -1),
//                    new Twig_Token(Twig_Token::OPERATOR_TYPE, "|", -1),
//                    new Twig_Token(Twig_Token::NAME_TYPE, "b", -1),
//                    new Twig_Token(Twig_Token::OPERATOR_TYPE, ":", -1),
//                    new Twig_Token(Twig_Token::STRING_TYPE, "param1", -1),
//                    new Twig_Token(Twig_Token::OPERATOR_TYPE, ":", -1),
//                    new Twig_Token(Twig_Token::STRING_TYPE, "param2", -1),
//                    new Twig_Token(Twig_Token::OPERATOR_TYPE, "|", -1),
//                    new Twig_Token(Twig_Token::NAME_TYPE, "modifier2", -1),
//                )
//            )
        );
    }
}