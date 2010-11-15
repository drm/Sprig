<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader_FunctionTokenParser extends Sprig_TokenParser_Smarty_Function implements Sprig_Extension_Smarty_PluginLoader_PluginInterface
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

    public function parse(Twig_Token $token)
    {
        $ret = parent::parse($token);
        $ret->setPluginFile($this->pluginFile);
        return $ret;
    }

    public function getNodeImpl()
    {
        return 'Sprig_Extension_Smarty_PluginLoader_Function';
    }
}