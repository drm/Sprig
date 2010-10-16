<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Capture extends Sprig_TokenParser_Smarty_Block {
    function getNodeImpl()
    {
        return 'Sprig_Node_Smarty_Capture';
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return 'capture';
    }



    
}