<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Lexer implements Twig_LexerInterface {
    public static $regex = array(
        'name'          => '[a-zA-Z_][a-zA-Z0-9_]*',
        'number'        => '[0-9]+(?:\.[0-9]+)?',
        'string'        => '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')',
        'operator'      => '(<=?|>=?|[!=]=|=|\/\/|\.\.|[().,%*\/+~]|\[|\]|\?|\:|&&|\|[@|]?|->?|!)',
        'whitespace'    => '\s+',
        'block_start'   => '\{(?=\/?[a-zA-Z_][a-zA-Z0-9_]*)',
        'block_end'     => '\}',
        'comment_start' => '\{\*',
        'comment_end'   => '\*\}',
        'var_start'     => '\{',
        'var_end'       => '\}',
        'var'           => '\$([a-zA-Z_][a-zA-Z0-9_]*)',
        'config'        => '#([a-zA-Z_][a-zA-Z0-9_]*)#'
    );
    
    public static $operatorTokenCompat = array(
        '&&' => array(Twig_Token::NAME_TYPE,    'and'),
        '||' => array(Twig_Token::NAME_TYPE,    'or'),
        '!'  => array(Twig_Token::NAME_TYPE,    'not'),
        '|@' => array(Twig_Token::OPERATOR_TYPE, '|'),
        '->' => array(Twig_Token::OPERATOR_TYPE, '.')
    );
    
    public static $genericTokenMap = array(
        'var'           => array(Sprig_Token::VAR_TYPE, 1),
        'config'        => array(Sprig_Token::CONFIG_TYPE, 1),
        'name'          => array(Twig_Token::NAME_TYPE, 0),
        'number'        => array(Twig_Token::NUMBER_TYPE, 0),
        'string'        => array(Twig_Token::STRING_TYPE, 0),
        'operator'      => array(Twig_Token::OPERATOR_TYPE, 0)
    );

    protected $env = null;

    private $ptr;
    private $code;
    private $len;
    private $tokens;
    private $line = 1;
    private $linesScannedPtr = 0;


    function __construct(Sprig_Environment $env = null) 
    {
        if($env) {
            $this->setEnvironment($env);
        }
    }


    public function setEnvironment(Sprig_Environment $env) 
    {
        $this->env = $env;
    }


    /**
     * @throws UnexpectedValueException
     * @param  $code
     * @param string $filename
     * @return Twig_TokenStream
     */
    public function tokenize($code, $filename = 'n/a')
    {
        $this->code = $code;
        $this->len = strlen($this->code);
        $this->ptr = 0;
        $this->tokens = array();

        $isErr = false;

        while($this->ptr < $this->len) {
            // sanity check
            $prePtr = $this->ptr;

            if(preg_match('/^\{\s*(literal|raw|php)\s*}(.*?)\{(\s*\/\s*|\s*end)(\\1)\s*}/s', substr($this->code, $this->ptr), $match)) {
                $this->ptr += strlen($match[0]);
                if($match[1] == 'php' && $this->isCompat(Sprig_Environment::COMPAT_PHP_BLOCKS)) {
                    $this->tokens[]= new Sprig_Token(Sprig_Token::PHP_TYPE, $match[2], $this->line());
                } else {
                    $data .= $match[2];
                }
            } else {
                $data = $this->skipAhead(array(
                    self::$regex['comment_start'],
                    self::$regex['block_start'],
                    self::$regex['var_start']
                ));
                
                if($data) {
                    $this->tokens[] = new Twig_Token(Twig_Token::TEXT_TYPE, $data, $this->line());
                    $data = '';
                }
                
                if(false !== $this->isMatch(self::$regex['comment_start'])) {
                    $this->skipAhead(self::$regex['comment_end'], true);
                } elseif(
                    false !== ($start = $this->isMatch(array(self::$regex['block_start'], self::$regex['var_start']), 0, false))
                ) {
                    if($this->isMatch(self::$regex['block_start'])) {
                        $this->tokens[] = new Twig_Token(Twig_Token::BLOCK_START_TYPE, '', $this->line());
                        if($this->isMatch('\/') && $name = $this->isMatch(self::$regex['name'])) {
                            $this->tokens[]= new Twig_Token(Twig_Token::NAME_TYPE, 'end' . $name, $this->line());
                        }
                        $end = 'block_end';
                    } elseif($this->isMatch(self::$regex['var_start'])) {
                        $this->tokens[] = new Twig_Token(Twig_Token::VAR_START_TYPE, '', $this->line());
                        $end = 'var_end';
                    } else {
                        throw new LogicException("This should be unreachable code!");
                    }
                    while(!$isErr) {
                        $prePtr2 = $this->ptr;
                        
                        $match = false;
                        $this->skipWhitespace();
                        foreach(self::$genericTokenMap as $regexName => $type) {
                            list($tokenType, $matchIndex) = $type;
                            $match = $this->isMatch(self::$regex[$regexName], $matchIndex);
                            if(false !== $match) {
                                if($tokenType == Twig_Token::OPERATOR_TYPE && array_key_exists($match, self::$operatorTokenCompat)) {
                                    $this->tokens[]= new Twig_Token(
                                        self::$operatorTokenCompat[$match][0], 
                                        self::$operatorTokenCompat[$match][1], 
                                        $this->line()
                                    );
                                } else {
                                    $this->tokens[]= new Twig_Token($tokenType, $match, $this->line());
                                }
                                if($tokenType == Twig_Token::VAR_END_TYPE || $tokenType == Twig_Token::BLOCK_END_TYPE) {
                                    break 2;
                                }
                                
                                break;
                            }
                        }
                        if(false === $match) {
                            if($match = $this->isMatch(self::$regex[$end])) {
                                switch($end) {
                                    case 'var_end':
                                        $this->tokens[]= new Twig_Token(Twig_Token::VAR_END_TYPE, $match, $this->line());
                                    break;
                                    case 'block_end':
                                        $this->tokens[]= new Twig_Token(Twig_Token::BLOCK_END_TYPE, $match, $this->line());
                                    break;
                                    default:
                                        throw new LogicException("This should be unreachable code!");
                                }
                                break;
                            } else {
                                $isErr = true;
                            }
                        }
                        if($this->ptr == $prePtr2 || $isErr) {
                            throw new UnexpectedValueException("expr Unexpected input at offset $this->ptr near " . substr($this->code, $this->ptr, 10));
                        }
                    }
                }
            }

            if($this->ptr == $prePtr || $isErr) {
                throw new UnexpectedValueException("Unexpected input at offset $this->ptr near " . substr($this->code, $this->ptr, 10));
            }
        }
        if($data) {
            $this->tokens[] = new Twig_Token(Twig_Token::TEXT_TYPE, $data, $this->line());
        }
        $this->tokens[]= new Twig_Token(Twig_Token::EOF_TYPE, '', $this->line());
        return new Twig_TokenStream($this->tokens, $filename);
    }
    
    
    
    protected function isMatch($pattern, $returnGroup = 0, $increasePointer = true) 
    {
        $ret = false;
        if(is_array($pattern)) {
            foreach($pattern as $p) {
                $ret = $this->isMatch($p, $returnGroup, $increasePointer);
                if(false !== $ret) {
                    break;
                }
            }
        } else {
            if(preg_match('/' . $pattern . '/As', substr($this->code, $this->ptr), $m)) {
                if($increasePointer) {
                    $this->ptr += strlen($m[0]);
                }
                $ret = $m[$returnGroup];
            }
        }
        return $ret;
    }
    
    
    protected function skipWhitespace()
    {
        while(ctype_space($this->code{$this->ptr}))
            $this->ptr ++;
    }

    
    public function line() 
    {
        if($this->linesScannedPtr < $this->ptr) {
            for( ;$this->linesScannedPtr <= $this->ptr; $this->linesScannedPtr ++) {
                if($this->linesScannedPtr >= $this->len) {
                    break;
                }
                if($this->code{$this->linesScannedPtr} == "\n") {
                    $this->line ++;
                }
            }
        }
        return $this->line;
    }
    


    protected function skipAhead($until, $inclusive = false)
    {
        // TODO refactor this to a non-anchored regex.
        $i = $this->ptr;
        $match = $this->isMatch($until, 0, $inclusive);
        while(!$match) {
            $this->ptr ++;
            if($this->ptr >= $this->len) {
                break;
            }
            $match = $this->isMatch($until, 0, $inclusive);
        }
        return substr($this->code, $i, $this->ptr - $i);
    }


    public function isCompat($compat) 
    {
        if($this->env) {
            return $this->env->isCompat($compat);
        }
        return true;
    }
}
