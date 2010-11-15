<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader_Iterator extends ArrayIterator implements RecursiveIterator {
    protected $type;

    function __construct($pluginDirs, $type) {
        if(!in_array($type, array('modifier', 'function', 'block'))) {
            throw new InvalidArgumentException("Unsupported type '$type'");
        }
        parent::__construct($pluginDirs);
        $this->type = $type;
        $this->pattern = "~/$type\\..*\\.php$~";
    }

    public function getChildren()
    {
        return new RecursiveRegexIterator(new RecursiveDirectoryIterator($this->current()), $this->pattern);
    }


    public function hasChildren() {
        return count($this->getChildren());
    }
}

