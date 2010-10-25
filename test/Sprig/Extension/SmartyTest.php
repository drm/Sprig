<?php


class Sprig_Extension_SmartyTest extends PHPUnit_Framework_TestCase {
    public static $tags = array(
        'foreach',
        'section',
        'include',
        'assign',
        'capture'
    );
    
    
    function setUp() {
        sort(self::$tags);
    }

    /**
     * @covers Sprig_Extension_Smarty::__construct
     * @covers Sprig_Extension_Smarty::getFilters
     * @covers Sprig_Extension_Smarty::getTokenParsers
     */
    function testDefaultConstruction() {
        $ext = new Sprig_Extension_Smarty();
        $this->assertEquals(array(), $ext->getFilters());
        
        $actual = array();
        foreach($ext->getTokenParsers() as $parser) {
            $actual[] = $parser->getTag();
        }
        
        sort($actual);
        
        $this->assertEquals(self::$tags, $actual);
    }
    
    /**
     * @covers Sprig_Extension_Smarty::__construct
     * @covers Sprig_Extension_Smarty::getFilters
     */
    function testFiltersAsConstructionArguments() {
        $ext = new Sprig_Extension_Smarty(array(), array('foo'));
        $filters = $ext->getFilters();
        $this->assertEquals(1, count($filters));
        $filter = array_shift($filters);
        $this->assertType('Twig_Filter_Function', $filter);
        $this->assertEquals('smarty_modifier_foo', $filter->compile());
    }
    
    
    /**
     * @covers Sprig_Extension_Smarty::__construct
     * @covers Sprig_Extension_Smarty::getTokenParsers
     */
    function testFunctionsAsConstructorArguments() {
        $ext = new Sprig_Extension_Smarty(array('foo'), array());
        $parsers = $ext->getTokenParsers();
        
        foreach($parsers as $i => $parser) {
            if(in_array($parser->getTag(), self::$tags)) {
                unset($parsers[$i]);
            }
        }
        
        $this->assertEquals(1, count($parsers));
        $parser = array_shift($parsers);
        $this->assertType('Sprig_TokenParser_Smarty_Function', $parser);
        $this->assertEquals('foo', $parser->getTag());
    }
    
    
    /**
     * @covers Sprig_Extension_Smarty::getName
     */
    function testNameIsSmarty() {
        // this, of course, is just a coverage formality :P
        
        $ext = new Sprig_Extension_Smarty();
        $this->assertEquals('smarty', $ext->getName());
    }
}
