<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty extends Twig_Node {
    function __construct($tagName, array $parameters, $body, $lineNo) {
        parent::__construct(
            array(
                 'parameters' => new Sprig_Node_Smarty_ParameterList($parameters),
                 'body' => $body
            ),
            array(
                 'tag' => $tagName
            ),
            $lineNo,
            $tagName
        );
    }


    function hasParameter($name) {
        return $this->getNode('parameters')->hasNode($name);
    }


    function getParameter($name) {
        return $this->getNode('parameters')->getNode($name);
    }
}