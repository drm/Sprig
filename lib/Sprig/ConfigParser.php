<?php


class Sprig_ConfigParser
{
    function __construct()
    {
        $this->lexer = new Sprig_Lexer;
    }


    function parse($configData)
    {
        $i = 0;
        $section = null;
        $values = array();
        while($i < strlen($configData)) {
            $str = substr($configData, $i);
            // comment and empty lines
            if(preg_match('/^\s*(#.*)?(\n|$)/', $str, $m)) {
                $i += strlen($m[0]);
            } elseif(preg_match('/^\s*(.+?)\s*=\s*/', $str, $m)) {
                $varName = $m[1];
                $i += strlen($m[0]);
                $str = substr($configData, $i);
                // TODO, DRY this up :)
                if(preg_match('/^"""(.*)"""\s*(\n|$)/s', $str, $m)) {
                    $value = $m[1];
                    $i += strlen($m[0]);
                } elseif(preg_match('/^(' . $this->lexer->regex['double_string'] . ')\s*(?:\n|$)/', $str, $m)) {
                    $value = $m[1];
                    $i += strlen($m[0]);
                } elseif(preg_match('/^(' . $this->lexer->regex['number'] .')\s*?(?:\n|$)/', $str, $m)) {
                    $value = $m[0];
                    $i += strlen($m[0]);
                } elseif(preg_match('/^(' . $this->lexer->regex['name'] . ')\s*?(?:\n|$)/', $str, $m)) {
                    $value = $this->getNameValue($m[1]);
                    $i += strlen($m[0]);
                } else {
                    preg_match('/^(.*?)(\n|$)/', $str, $m);
                    $value = $m[1];
                    $i += strlen($m[0]);
                }
                if($section == null) {
                    $values[$varName]= $value;
                } else {
                    $values[$section][$varName]= $value;
                }
            } elseif(preg_match('/^\[([^\]]+)\]\s*(?:\n|$)/', $str, $m)) {
                $section = $m[1];
                $i += strlen($m[0]);
            } else {
                throw new Exception("Syntax error in config file, unexpected input at offset $i near " . substr($str, max(0, $i -3), 10));
            }
        }
        return $values;
    }


    function getNameValue($name)
    {
        if(in_array($name, array('true', 'on', 'yes'))) {
            $ret = true;
        } elseif(in_array($name, array('false', 'off', 'no'))) {
            $ret = false;
        } elseif($name == 'null') {
            $ret = null;
        } else {
            $ret = $name;
        }
        return $ret;
    }
}