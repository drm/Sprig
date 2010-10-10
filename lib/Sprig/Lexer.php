<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Lexer implements Twig_LexerInterface {
    const LINE_NO_STUB = -1; // all usages should be replaced with appropriate line numbers; temporary stub

    public static $regex = array(
        'name' => '[a-zA-Z_][a-zA-Z0-9_]*',
        'number' => '[0-9]+(?:\.[0-9]+)?',
        'string' => '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')',
        'operator' => '(<=?|>=?|[!=]=|=|\/\/|\.\.|[(){}.,%*\/+~|-]|\[|\]|\?|\:)'
    );

    protected $env = null;

    private $ptr;
    private $code;
    private $len;
    private $tokens;


    function __construct(Sprig_Environment $env = null) {
        if($env) {
            $this->setEnvironment($env);
        }
    }


    public function setEnvironment(Sprig_Environment $env) {
        $this->env = $env;
    }


    private function isCompat($compat) {
        if($this->env) {
            return $this->env->isCompat($compat);
        }
        return true;
    }


    public function tokenize($code, $filename = 'n/a')
    {
        $this->code = $code;
        $this->len = strlen($this->code);
        $this->ptr = 0;
        $this->tokens = array();

        $data = null;
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
            } elseif($this->code[$this->ptr] == '{') {
                if($data) {
                    $this->tokens[] = new Twig_Token(Twig_Token::TEXT_TYPE, $data, $this->line());
                    $data = null;
                }

                $this->ptr ++;
                $this->skipWhitespace();

                // block
                if(preg_match('/^\/?' . self::$regex['name'] . '/', substr($this->code, $this->ptr), $match)) {
                    $this->ptr += strlen($match[0]);
                    $this->tokens[]= new Twig_Token(Twig_Token::BLOCK_START_TYPE, '', $this->line());
                    $name = $match[0];
                    if($name{0} == '/') {
                        $name = 'end' . substr($name, 1);
                    }
                    $this->tokens[]= new Twig_Token(Twig_Token::NAME_TYPE, $name, $this->line());
                    if(!$this->expr()) {
                        $isErr = true;
                    }
                    $this->ptr ++;
                    $this->tokens[]= new Twig_Token(Twig_Token::BLOCK_END_TYPE, '', $this->line());
                } elseif($this->code[$this->ptr] == '*') {
                    if($this->lookahead('*}')) {
                        $this->ptr += 2;
                    }
                } else {
                    $this->tokens[]= new Twig_Token(Twig_Token::VAR_START_TYPE, '', $this->line());
                    if(!$this->expr()) {
                        $isErr = true;
                    }
                    $this->ptr ++;
                    $this->tokens[]= new Twig_Token(Twig_Token::VAR_END_TYPE, '', $this->line());
                }
            } else {
                $data .= $this->lookahead('{');
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


    protected function expr($end = '}') {
        while($this->ptr < $this->len) {
            $prePtr = $this->ptr;
            $this->skipWhitespace();
            if($this->code{$this->ptr} == $end) {
                break;
            } elseif(preg_match('/^' . self::$regex['operator'] . '/s', substr($this->code, $this->ptr), $match)) {
                if($this->isCompat(Sprig_Environment::COMPAT_CONVERT_ARROW_TO_DOT) && substr($this->code, $this->ptr, 2) == '->') {
                    $this->tokens[]= new Twig_Token(Twig_Token::OPERATOR_TYPE, '.', $this->line());
                    $this->ptr += 2;
                } elseif($this->isCompat(Sprig_Environment::COMPAT_CONVERT_LOGICAL_OPERATORS) && substr($this->code, $this->ptr, 2) == '||') {
                    $this->tokens[]= new Twig_Token(Twig_Token::NAME_TYPE, 'or', $this->line());
                    $this->ptr += 2;
                } else {
                    $this->tokens[]= new Twig_Token(Twig_Token::OPERATOR_TYPE, $match[0], $this->line());
                    $this->ptr += strlen($match[0]);
                }
                if(
                        $this->isCompat(Sprig_Environment::COMPAT_DROP_MODIFIER_AT_SIGN)
                        && $match[0] == '|'
                        && $this->code{$this->ptr} == '@'
                ) {
                    $this->ptr ++;
                }
            } else {
                if($this->code{$this->ptr} == '$' && preg_match('/^' . self::$regex['name'] . '/', substr($this->code, $this->ptr +1), $match)) {
                    $this->ptr ++;
                    $this->tokens[]= new Sprig_Token(Sprig_Token::VAR_TYPE, $match[0], $this->line());
                    $this->ptr += strlen($match[0]);

//                    if($this->code{$this->ptr} == ':' && $this->isCompat(Sprig_Environment::COMPAT_CONVERT_MODIFIER_ARGUMENTS) && $this->tokens[count($this->tokens) -1]->test(Twig_Token::OPERATOR_TYPE, '|') ) {
//                        $this->code{$this->ptr} = '(';
//                        for($i = $this->ptr; $i < $this->len; $i ++) {
//                            if($this->code{$i} == ':') {
//                                $this->code{$i} = ',';
//                            } elseif($this->code{$i} == '}' || $this->code{$i} == '|') {
//                                $this->code = substr($this->code, 0, $i) . ')' . substr($this->code, $i);
//                                break;
//                            }
//                        }
//                    }
                } elseif(preg_match('/^' . self::$regex['name'] . '/', substr($this->code, $this->ptr), $match)) {
                    $this->tokens[]= new Twig_Token(Twig_Token::NAME_TYPE, $match[0], $this->line());
                    $this->ptr += strlen($match[0]);
                } elseif(preg_match('/^' . self::$regex['string'] . '/', substr($this->code, $this->ptr), $match)) {
                    $value = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
                    $this->tokens[]= new Twig_Token(Twig_Token::STRING_TYPE, $value, $this->line());
                    $this->ptr += strlen($match[0]);
                } elseif(preg_match('/^' . self::$regex['number'] . '/', substr($this->code, $this->ptr), $match)) {
                    $this->tokens[]= new Twig_Token(Twig_Token::NUMBER_TYPE, $match[0], $this->line());
                    $this->ptr += strlen($match[0]);
                } elseif($this->isCompat(Sprig_Environment::COMPAT_CONVERT_LOGICAL_OPERATORS)) {
                    $repl = null;
                    if($this->code{$this->ptr} == '!') {
                        $repl = 'not';
                        $this->ptr ++;
                    } else {
                        switch(substr($this->code, $this->ptr, 2)) {
                            case '&&':
                                $repl = 'and';
                                $this->ptr += 2;
                                break;
                            case '||':
                                $repl = 'or';
                                $this->ptr += 2;
                                break;
                        }
                    }
                    if($repl) {
                        $this->tokens[]= new Twig_Token(Twig_Token::NAME_TYPE, $repl, $this->line());
                    }
                }
            }
            if($this->ptr == $prePtr) {
                return false;
            }
        }
        return true;
    }


    private $line = 1;
    private $linesScannedPtr = 0;
    
    function line() {
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
    


    protected function lookahead($until) {
        $len = strlen($until);
        $i = $this->ptr;
        while(substr($this->code, $this->ptr, $len) != $until) {
            $this->ptr ++;
            if($this->ptr >= $this->len) {
                break;
            }
        }
        return substr($this->code, $i, $this->ptr - $i);
    }


    protected function skipWhitespace()
    {
        while(ctype_space($this->code{$this->ptr}))
            $this->ptr ++;
    }
}