<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Module extends Twig_Node_Module
{
    protected $plugins = array();

    function addPlugin(Sprig_Extension_Smarty_PluginLoader_PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }

    protected function compileDisplayBody(Twig_Compiler $compiler)
    {
        $this->compilePluginRequirements($compiler);
        $compiler->write('$this->_tpl_vars =& $context;');
        parent::compileDisplayBody($compiler);
    }


    protected function compilePluginRequirements($compiler)
    {
        $requires = array();
        foreach ($this->plugins as $plugin) {
            $requires[] = $plugin->getPluginFile();
        }
        foreach (array_unique($requires) as $require) {
            $compiler
                    ->write('require_once ')
                    ->repr($require)
                    ->raw(";\n");
        }
    }
}