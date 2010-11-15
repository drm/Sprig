<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

interface Sprig_Extension_Smarty_PluginLoader_PluginInterface {
    function setPluginFile($file);
    function getPluginFile();
}