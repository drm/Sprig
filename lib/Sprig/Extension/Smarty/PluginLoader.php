<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader extends Twig_Extension
{
    protected $pluginDirs = array();

    function __construct(array $dirs = array())
    {
        foreach ($dirs as $dir) {
            $this->addPluginDir($dir);
        }
    }

    public function getNodeVisitors()
    {
        return array(
            new Sprig_Extension_Smarty_PluginLoader_NodeVisitor()
        );
    }


    function getTokenParsers()
    {
        $ret = array();
        foreach ($this->getPluginFileIterator('function') as $file) {
            $pluginName = basename($this->getPluginName($file, 'function'));
            $ret[$pluginName] = new Sprig_Extension_Smarty_PluginLoader_FunctionTokenParser($pluginName);
            $ret[$pluginName]->setPluginFile($file);
        }
        foreach ($this->getPluginFileIterator('block') as $file) {
            $pluginName = basename($this->getPluginName($file, 'block'));
            $ret[$pluginName] = new Sprig_Extension_Smarty_PluginLoader_BlockTokenParser($pluginName);
            $ret[$pluginName]->setPluginFile($file);
        }

        return $ret;
    }

    public function getFilters()
    {
        $ret = array();
        foreach ($this->getPluginFileIterator('modifier') as $file) {
            $pluginName = basename($this->getPluginName($file, 'modifier'));
            $ret[$pluginName] = new Sprig_Extension_Smarty_PluginLoader_Filter('smarty_modifier_' . $pluginName);
            $ret[$pluginName]->setPluginFile($file);
        }
        return $ret;
    }


    function getPluginName($fileName, $type)
    {
        return substr(basename($fileName), strlen($type) + 1, -4); // e.g. [modifier.]w00t[.php]
    }


    function getPluginFileIterator($type)
    {
        $iterator = new RecursiveIteratorIterator(new Sprig_Extension_Smarty_PluginLoader_Iterator($this->pluginDirs, $type));
        $iterator->setMaxDepth(1);
        return $iterator;
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'pluginLoader';
    }

    public function addPluginDir($dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException($dir);
        }
        $this->pluginDirs[] = $dir;
    }

}
