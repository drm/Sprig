<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_Extension_Smarty extends Twig_Extension {
    public $functions;
    public $modifiers;
    
    function __construct(array $functions = array(), array $modifiers = array()) {
        $this->functions = $functions;
        $this->modifiers = $modifiers;
    }


    function getTokenParsers() {
        $ret = array(
            new Sprig_TokenParser_Smarty_Foreach(),
            new Sprig_TokenParser_Smarty_Include(),
        );

        foreach($this->functions as $function) {
            $ret[]= new Sprig_TokenParser_Smarty_Function($function);
        }
        return $ret;
    }

    
    public function getFilters()
    {
        $ret = array();
        foreach($this->modifiers as $name) {
            $ret[$name]= new Twig_Filter_Function('smarty_modifier_' . $name);
        }
        return $ret;
    }




    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'smarty';
    }


}