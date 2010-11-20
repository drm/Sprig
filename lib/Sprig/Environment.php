<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Environment extends Twig_Environment
{
    public static $compatDefault = array(
        self::COMPAT_DROP_MODIFIER_AT_SIGN,
        self::COMPAT_CONVERT_ARROW_TO_DOT,
        self::COMPAT_CONVERT_LOGICAL_OPERATORS,
        self::COMPAT_PHP_BLOCKS,
        self::COMPAT_CONVERT_MODIFIER_ARGUMENTS,
    );


    const COMPAT_DROP_MODIFIER_AT_SIGN = 1;
    const COMPAT_CONVERT_ARROW_TO_DOT = 2;
    const COMPAT_CONVERT_LOGICAL_OPERATORS = 3;
    const COMPAT_PHP_BLOCKS = 4;
    const COMPAT_CONVERT_MODIFIER_ARGUMENTS = 5;

    protected $compat;


    function __construct(Twig_LoaderInterface $loader = null, $options = array(), Twig_LexerInterface $lexer = null, Twig_ParserInterface $parser = null, Twig_CompilerInterface $compiler = null)
    {
        parent::__construct(
            $loader,
            $options,
            is_null($lexer) ? new Sprig_Lexer() : $lexer,
            is_null($parser) ? new Sprig_Parser() : $lexer,
            is_null($compiler) ? new Sprig_Compiler() : $lexer
        );

        $this->compat = isset($options['compat']) ? (array)$options['compat'] : self::$compatDefault;
    }


    function isCompat($compat)
    {
        return in_array($compat, $this->compat);
    }
}