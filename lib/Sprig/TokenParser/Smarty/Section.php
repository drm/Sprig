<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Section extends Sprig_TokenParser_Smarty_Block {
    public function getTag()
    {
        return 'section';
    }

    function getNodeImpl()
    {
        return 'Sprig_Node_Smarty_Section';
    }
}