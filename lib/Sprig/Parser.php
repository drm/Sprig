<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Parser extends Twig_Parser
{
    public function __construct(Twig_Environment $env = null)
    {
        parent::__construct($env);
        $this->expressionParser = new Sprig_ExpressionParser($this);
    }
}