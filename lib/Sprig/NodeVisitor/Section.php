<?php

class Sprig_NodeVisitor_Smarty_Section implements Twig_NodeVisitorInterface
{
    protected $sectionStack = 0;
    protected $getAttrStack = 0;

    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof Sprig_Node_Smarty_Section) {
            $this->sectionStack--;
        } elseif ($node instanceof Twig_Node_Expression_GetAttr) {
            $this->getAttrStack--;
        }
        return $node;
    }


    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof Sprig_Node_Smarty_Section) {
            $this->sections[] = $node->getAttribute('name');
        } elseif ($node instanceof Twig_Node_Expression_GetAttr) {
            $this->getAttrStack++;
        }
        return $node;
    }

}