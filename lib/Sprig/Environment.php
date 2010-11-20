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

    private $smartySettings = array(
        'request_use_auto_globals'  => true,
        'security'                  => false,
        'debugging'                 => false,
        'config_dir'                => 'configs',
        'force_compile'             => false,
        'compile_check'             => true
    );

    private $isSmartyAllowed = array(
        'eval' => false,
    );


    function __construct(Twig_LoaderInterface $loader = null, $options = array(), Twig_LexerInterface $lexer = null, Twig_ParserInterface $parser = null, Twig_CompilerInterface $compiler = null)
    {
        if(!array_key_exists('base_template_class', $options)) {
            $options['base_template_class'] = 'Sprig_Template';
        }
        if(!array_key_exists('trim_blocks', $options)) {
            $options['trim_blocks'] = true;
        }
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



    function getSmartyProperty($name)
    {
        if(!array_key_exists($name, $this->smartySettings)) {
            throw new Exception("Property $name is unknown. If this is a bug, please report!");
        }
        return $this->smartySettings[$name];
    }

    
    function setSmartyProperty($name, $value)
    {
        if(!array_key_exists($name, $this->smartySettings)) {
            throw new Exception("Property $name is unknown. If this is a bug, please report!");
        }
        $this->smartySettings[$name] = $value;
    }


    function __set($name, $value)
    {
        $this->setSmartyProperty($name, $value);
    }


    function getConfig($fileName, $section = null)
    {
        // TODO compile & cache
        $ret = false;
        foreach((array) $this->getSmartyProperty('config_dir') as $dir) {
            if(is_dir($dir) && is_file($file = rtrim($dir, '/') . '/' . $fileName)) {
                $parser = new Sprig_ConfigParser();
                $ret = $parser->parse(file_get_contents($file));
            }
        }
        if(!is_null($section)) {
            $ret = array_key_exists($section, $ret) ? $ret[$section] : array();
        }
        
        return $ret;
        die();
    }
}