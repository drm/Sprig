<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_ConfigLoad extends Sprig_TokenParser_Smarty_Tag
{
    function getNodeImpl()
    {
        return 'Sprig_Node_Smarty_ConfigLoad';
    }

    function getTag()
    {
        return 'config_load';
    }
}