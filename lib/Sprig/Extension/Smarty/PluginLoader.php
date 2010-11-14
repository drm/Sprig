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
    
    
    public function getFilters()
    {
        $ret = array();
        foreach($this->pluginDirs as $dir) {
            foreach(new RegexIterator(new DirectoryIterator($dir), '/^modifier..*\.php/') as $modifierFile) {
                $modifierName = substr($modifierFile, 9, -4);
                $ret[$modifierName] = new Twig_Filter_Function("smarty_modifier_$modifierName");
                //TODO lazyload file
                require_once rtrim($dir, '/') . '/' . $modifierFile;
            }
        }
        return $ret;
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
