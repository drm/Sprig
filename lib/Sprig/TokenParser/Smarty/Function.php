<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_TokenParser_Smarty_Function extends Sprig_TokenParser_Smarty_Tag {
    function __construct($functionName) {
        $this->functionName = $functionName;
    }


    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return $this->functionName;
    }



    public function getNodeImpl()
    {
        return 'Sprig_Node_Smarty_Function';
    }
}
