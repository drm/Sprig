<?php

class Sprig_ConfigParserTest extends PHPUnit_Framework_TestCase {
    /**
     * @param  $configFile
     * @param  $expectedDataFile
     * @return void
     *
     * @dataProvider testData
     */
    function testConfigParser($configFile, $expectedDataFile)
    {
        $parser = new Sprig_ConfigParser();
        $expectedData = include $expectedDataFile;
        $this->assertEquals($expectedData, $parser->parse(file_get_contents($configFile)));
    }


    function testData() {
        $ret = array();
        foreach(new RegexIterator(new DirectoryIterator(dirname(__FILE__) . '/../assets/configparser/'), '/\.conf$/') as $file) {
            $ret[]= array($file->getPathname(), $file->getPathname() . '.php');
        }
        return $ret;
    }
}