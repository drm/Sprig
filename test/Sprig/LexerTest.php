<?php

class Sprig_LexerTest_Sprig_EnvironmentStub extends Sprig_Environment
{
    public $isCompat = false;

    function isCompat()
    {
        return $this->isCompat;
    }
}


class Sprig_LexerTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->lexer = new Sprig_Lexer();
    }

    /**
     * @dataProvider tokenSequences
     */
    function testTokenSequences($expect, $in)
    {
        $this->assertLexicalEquivalence($expect, $this->lexer->tokenize($in));
    }


    /**
     * @dataProvider compatSettings
     */
    function testEnv($setting)
    {
        $stub = new Sprig_LexerTest_Sprig_EnvironmentStub();
        $stub->isCompat = true;
        $lexer = new Sprig_Lexer($stub);
        $default = new Sprig_Lexer();
        $this->assertTrue($lexer->isCompat($setting));
        $this->assertTrue($default->isCompat($setting));
        $stub->isCompat = false;
        $this->assertFalse($lexer->isCompat($setting));
        $this->assertTrue($default->isCompat($setting));
    }


    function testLineCounter()
    {
        $str = 'line {1}' . "\n";
        $str .= '{block 2}{* at line 2 *}' . "\n";
        $str .= '{$var 3}{* at line 3 *}' . "\n";
        $str .= '{"string' . "\n";
        $str .= '"}{5}' . "\n";
        $str .= 'final line {6}';

        $tokens = array();
        $stream = $this->lexer->tokenize($str);
        $i = 1;
        while (!$stream->isEOF()) {
            if ($stream->test(Twig_Token::STRING_TYPE)) {
                // the string spans two lines
                $i++;
            }
            if ($stream->test(Twig_Token::NUMBER_TYPE)) {
                $this->assertEquals($i++, $stream->getCurrent()->getValue());
            }
            $stream->next();
        }
    }


    /**
     * @dataProvider tokenizerErrors
     * @expectedException Exception
     */
    function testErrorsAreThrown($str)
    {
        $this->lexer->tokenize($str);
    }


    function assertLexicalEquivalence($tokenTypes, $tokenStream)
    {
        foreach ($tokenTypes as $token) {
            if ($tokenStream->isEOF()) {
                $this->assertFalse(true, 'Unexpected end of input');
            }
            $tokenIn = $tokenStream->getCurrent();
            if (is_string($token)) {
                $this->assertEquals($token, (string)$tokenIn);
            } elseif (is_array($token)) {
                $this->assertEquals($token[0], $tokenIn->getType());
                $this->assertEquals($token[1], $tokenIn->getValue());
            } elseif (is_int($token)) {
                $this->assertEquals($token, $tokenIn->getType());
            } else {
                throw new UnexpectedValueException("Invalid assertion data");
            }

            $tokenStream->next();
        }
        if (!$tokenStream->isEOF()) {
            $this->assertFalse(true, "Unexpected end of expected input");
        }
    }


    function tokenSequences()
    {
        $ret = array();
        $ret[] = array(array(), '');
        $ret[] = array(array(Twig_Token::TEXT_TYPE), ' ');
        $ret[] = array(array(Twig_Token::TEXT_TYPE), 'data');
        $ret[] = array(array(Twig_Token::TEXT_TYPE), '{literal}data{/literal}');
        $ret[] = array(array(), '{* comment *}');
        $ret[] = array(array(Twig_Token::TEXT_TYPE, Twig_Token::VAR_START_TYPE, Sprig_Token::VAR_TYPE, Twig_Token::VAR_END_TYPE), 'data { $var }');
        $ret[] = array(array(Twig_Token::BLOCK_START_TYPE, Twig_Token::NAME_TYPE, Twig_Token::BLOCK_END_TYPE), '{block}');
        $ret[] = array(
            array(Twig_Token::BLOCK_START_TYPE, Twig_Token::NAME_TYPE, Twig_Token::BLOCK_END_TYPE,
                  Twig_Token::BLOCK_START_TYPE, Twig_Token::NAME_TYPE, Twig_Token::BLOCK_END_TYPE),
            '{block}{/block}'
        );
        $ret[] = array(
            array(Twig_Token::BLOCK_START_TYPE, Twig_Token::NAME_TYPE, Twig_Token::BLOCK_END_TYPE,
                  Twig_Token::BLOCK_START_TYPE, Twig_Token::NAME_TYPE, Twig_Token::BLOCK_END_TYPE),
            '{block}{/block}'
        );

        // compatibility features
        $ret[] = array(array(Sprig_Token::PHP_TYPE), '{php}data{/php}');
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Sprig_Token::VAR_TYPE, array(Twig_Token::OPERATOR_TYPE, '.'),
                  Twig_Token::NAME_TYPE, Twig_Token::VAR_END_TYPE),
            '{$foo.bar}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Sprig_Token::VAR_TYPE, array(Twig_Token::OPERATOR_TYPE, '.'),
                  Twig_Token::NAME_TYPE, Twig_Token::VAR_END_TYPE),
            '{$foo->bar}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Sprig_Token::VAR_TYPE, array(Twig_Token::OPERATOR_TYPE, '.'),
                  Twig_Token::NAME_TYPE, Twig_Token::VAR_END_TYPE),
            '{$foo->bar}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Twig_Token::NUMBER_TYPE, array(Twig_Token::NAME_TYPE, 'and'),
                  Twig_Token::NUMBER_TYPE, array(Twig_Token::NAME_TYPE, 'or'), array(Twig_Token::NAME_TYPE, 'not'),
                  Twig_Token::NUMBER_TYPE, Twig_Token::VAR_END_TYPE),
            '{ 1 && 2 || !3 }'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Twig_Token::NUMBER_TYPE, array(Twig_Token::NAME_TYPE, 'and'),
                  Twig_Token::NUMBER_TYPE, array(Twig_Token::NAME_TYPE, 'or'), array(Twig_Token::NAME_TYPE, 'not'),
                  Twig_Token::NUMBER_TYPE, Twig_Token::VAR_END_TYPE),
            '{ 1 and 2 or not 3 }'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Sprig_Token::VAR_TYPE, array(Twig_Token::OPERATOR_TYPE, '|'),
                  Twig_Token::NAME_TYPE, Twig_Token::VAR_END_TYPE),
            '{$var|@count}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, Twig_Token::STRING_TYPE, Twig_Token::VAR_END_TYPE),
            '{"foo"}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, array(Twig_Token::STRING_TYPE, "foo"),
                  array(Twig_Token::OPERATOR_TYPE, "~"), array(Sprig_Token::VAR_TYPE, "var"),
                  array(Twig_Token::OPERATOR_TYPE, "~"), array(Twig_Token::STRING_TYPE, "foo"),
                  Twig_Token::VAR_END_TYPE),
            '{"foo`$var`foo"}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, array(Twig_Token::STRING_TYPE, "foo"),
                  Twig_Token::VAR_END_TYPE),
            '{\'foo\'}'
        );
        $ret[] = array(
            array(Twig_Token::VAR_START_TYPE, array(Sprig_Token::CONFIG_TYPE, 'foo'), Twig_Token::VAR_END_TYPE),
            '{#foo#}'
        );

        return $ret;
    }


    function tokenizerErrors()
    {
        $ret = array();

        $ret[] = array('{ \ ');
        $ret[] = array('{ $var \ ');
        $ret[] = array('{block $var \ ');

        return $ret;
    }


    function compatSettings()
    {
        $refl = new ReflectionClass('Sprig_Environment');

        $ret = array();
        foreach ($refl->getConstants() as $name => $c) {
            if (preg_match('/^COMPAT_/', $name)) {
                $ret[] = array($name);
            }
        }
        return $ret;
    }
}
