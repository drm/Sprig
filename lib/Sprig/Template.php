<?php

abstract class Sprig_Template extends Twig_Template
{
    protected $_tpl_vars = array();

    function assign($name, $value)
    {
        $this->_tpl_vars[$name] = $value;
    }


    function trigger_error($msg)
    {
        throw new Sprig_RuntimeError($msg);
    }

    function _get_plugin_filepath($type, $file)
    {
        $ret = null;
        foreach ($this->getEnvironment()->getExtensions() as $ext) {
            if ($ext instanceof Sprig_Extension_Smarty_PluginLoader) {
                $ret = $ext->getPluginFilePath($type, $file);
                if (null !== $ret) {
                    break;
                }
            }
        }
        return $ret;
    }


    function __get($property)
    {
        return $this->getEnvironment()->getSmartyProperty($property);
    }
}
