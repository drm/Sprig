<?php

/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_NodeVisitor_StrictTests implements Twig_NodeVisitorInterface 
{
    protected $isInStrictTest = false;
    protected $tests;
    
    
    function __construct(array $tests)
    {
        $this->tests = $tests;
    }

    function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if($this->isStrictTest($node)) {
            $this->isInStrictTest = true;
        } elseif($this->isInStrictTest) {
            if($node instanceof Sprig_Node_Expression_Var) {
                $node->setCompileAsStrict(true);
            } elseif($node instanceof Twig_Node_Expression_Name) {
                $node = new Sprig_Node_Expression_Var($node->getAttribute('name'), $node->getLine());
                $node->setCompileAsStrict(true);
            } elseif($node instanceof Twig_Node_Expression_GetAttr) {
                $node = new Sprig_Node_Expression_GetAttr($node->getNode('node'), $node->getNode('attribute'), new Twig_Node(), Twig_Node_Expression_GetAttr::TYPE_ARRAY, $node->getLine());
                $node->setCompileAsStrict(true);
            } 
        } 
        return $node;
    }
    
    
    function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if($this->isStrictTest($node)) {
            $this->isInStrictTest = false;
        }
        return $node;
    }
    
    
    function isStrictTest(Twig_NodeInterface $node) 
    {
        return 
            $node instanceof Twig_Node_Expression_Test 
         && in_array($node->getAttribute('name'), $this->tests)
        ;
    }
}

