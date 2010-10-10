<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_Node_Smarty extends Twig_Node {
    function __construct($tagName, $attributes, $body) {
        parent::__construct();
        $this->tagName = $tagName;
        $this->attributes = $attributes;
        $this->body = $body;
    }
}