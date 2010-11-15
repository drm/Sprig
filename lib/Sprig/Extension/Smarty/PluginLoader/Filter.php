<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader_Filter extends Twig_Filter_Function implements Sprig_Extension_Smarty_PluginLoader_PluginInterface
{
    protected $pluginFile;

    public function setPluginFile($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function getPluginFile()
    {
        return $this->pluginFile;
    }
}