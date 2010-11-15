<?php

class Sprig_Extension_Smarty_PluginLoader_Block extends Sprig_Node_Smarty_Block implements Sprig_Extension_Smarty_PluginLoader_PluginInterface
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