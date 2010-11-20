<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

abstract class Sprig_Node_AbstractTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->context = array('a' => 0, 'b' => 2);
        $this->compiler = new Twig_Compiler();
        $this->compiler->setEnvironment(new Twig_Environment());
    }


    function assertExpressionResultEquals($expect, $expression)
    {
        $expression->compile($this->compiler);
        $context = $this->context;
        $code = '$result = ' . $this->compiler->getSource() . ';';
        eval($code);
        $this->assertEquals($expect, $result);
    }


    function _expr($var)
    {
        switch (true) {
            case is_array($var):
                $ret = array();
                foreach ($var as $k => $value) {
                    $ret[$k] = $this->_expr($value);
                }
                break;
            case is_bool($var):
                $ret = new Twig_Node_Expression_Name($var ? 'true' : 'false', -1);
                break;
            case is_numeric($var):
            case is_string($var):
                $ret = new Twig_Node_Expression_Constant($var, -1);
                break;
            case is_null($var):
                $ret = new Twig_Node_Expression_Name('null', -1);
                break;
            default:
                throw new Exception("Sorry, don't know how to express " . gettype($var) . " in old Twiggish");
        }
        return $ret;
    }
}
