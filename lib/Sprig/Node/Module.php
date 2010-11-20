<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Module extends Twig_Node_Module
{
    public function __construct(Twig_NodeInterface $body, Twig_Node_Expression $parent = null, Twig_NodeInterface $blocks, Twig_NodeInterface $macros, $filename)
    {
        parent::__construct($body, $parent, $blocks, $macros, $filename);
    }


    protected $plugins = array();

    function addPlugin(Sprig_Extension_Smarty_PluginLoader_PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }

    protected function compileDisplayBody(Twig_Compiler $compiler)
    {
        foreach ($this->plugins as $plugin) {
            $compiler
                    ->write('require_once ')
                    ->repr($plugin->getPluginFile())
                    ->raw(";\n");
        }
        parent::compileDisplayBody($compiler);
    }


}