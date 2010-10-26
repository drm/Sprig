<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Assign extends Sprig_TokenParser_Smarty_Tag {
    public function getTag()
    {
        return 'assign';
    }


    public function getNodeImpl()
    {
        return 'Sprig_Node_Smarty_Assign';
    }
}