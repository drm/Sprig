<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader extends Twig_Extension {
    protected $pluginDirs = array();
    
    function __construct(array $dirs = array()) {
        foreach($dirs as $dir) {
            $this->addPluginDir($dir);
        }
    }


    function getTokenParsers() 
    {
        
        //TODO
        return array();
    }

    public function getNodeVisitors()
    {
        return array(
            new Sprig_Extension_Smarty_PluginLoader_NodeVisitor()
        );
    }



    
    
    public function getFilters()
    {
        $ret = array();
        foreach($this->getPluginFileIterator('modifier') as $file) {
            $modifierName = basename($this->getPluginName($file, 'modifier'));
            $ret[$modifierName] = new Sprig_Extension_Smarty_PluginLoader_Filter('smarty_modifier_' . $modifierName);
            $ret[$modifierName]->setPluginFile($file);
        }
        return $ret;
    }


    function getPluginName($fileName, $type)
    {
        return substr(basename($fileName), strlen($type) +1, -4); // e.g. [modifier.]w00t[.php]
    }


    function getPluginFileIterator($type)
    {
        $iterator = new RecursiveIteratorIterator(new Sprig_Extension_Smarty_PluginLoader_Iterator($this->pluginDirs, 'modifier'));
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
        return 'smarty_plugins';
    }

    public function addPluginDir($dir)
    {
        if(!is_dir($dir)) {
            throw new InvalidArgumentException($dir);
        }
        $this->pluginDirs[]= $dir;
    }

}
