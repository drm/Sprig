<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht online <http://zicht.nl>
 */

class Sprig_Token extends Twig_Token {
    const VAR_TYPE = 10;
    const PHP_TYPE = 11;


    static function getTypeAsString($type, $short = false)
    {
        $ret = null;
        switch($type) {
            case self::VAR_TYPE:
                $name = 'VAR_TYPE';
                break;
            case self::PHP_TYPE:
                $name = 'PHP_TYPE';
                break;
            default:
                $ret = parent::getTypeAsString($type, $short);
                break;
        }
        if(is_null($ret)) {
            $ret = $short ? $name : __CLASS__ . '::' . $name;
        }

        return $ret;
    }


    function __toString()
    {
        return sprintf('%s(%s)', self::getTypeAsString($this->type, true), $this->value);
    }
}