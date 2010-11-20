<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */


class Sprig_Extension_StrictTests extends Twig_Extension
{
    function getTests()
    {
        return array(
            'isset' => new Twig_Test_Function('isset'),
            'empty' => new Twig_Test_Function('empty')
        );
    }


    function getNodeVisitors()
    {
        return array
        (
            'util' => new Sprig_NodeVisitor_StrictTests(array_keys($this->getTests()))
        );
    }


    function getName()
    {
        return __CLASS__;
    }
}
