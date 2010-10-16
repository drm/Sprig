<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

/**
 * @property Smarty $smarty
 * @property Sprig_Environment $sprig
 * @property array $testData
 * @property string $templateDir
 */

class SmartyIntegrationTest extends PHPUnit_Framework_TestCase {
    const TEMPLATE_DIR = '../assets/integration';
    function setUp() {
        if(is_writable(dirname(__FILE__) . '/../assets/tmp')) {
            $tmpDir = dirname(__FILE__) . '/../assets/tmp';
        } else {
            $tmpDir = sys_get_temp_dir();
        }
        $this->testData = array(
            'foo' => array(
                'bar' => 'baz'
            ),
            'foos' => array(
                array('bar1' => 'baz1'),
                array('bar2' => 'baz2'),
                array('bar3' => 'baz3')
            ),
            'var' => md5(str_repeat(chr(rand(65, 65+27)), 100))
        );
        $this->sprig = new Sprig_Environment(
            new Twig_Loader_Filesystem(dirname(__FILE__) . "/" . self::TEMPLATE_DIR),
            array(
                'cache' => $tmpDir,
                'debug' => true
            )
        );
        $this->sprig->addExtension(new Sprig_Extension_Smarty());

        $this->smarty = new Smarty();
        $this->smarty->compile_dir = $tmpDir;
        $this->smarty->template_dir = dirname(__FILE__) . "/" . self::TEMPLATE_DIR;
    }


    /**
     * @dataProvider templateFiles
     */
    function testSmartyAndSprigRenderEquivalent($file) {
        $this->smarty->assign($this->testData);
        $this->assertOutputIsEquivalent($this->smarty->fetch($file), $this->sprig->loadTemplate($file)->render($this->testData));
    }


    function assertOutputIsEquivalent($smartyOutput, $sprigOutput) {
        $this->assertEquals(trim(preg_replace('/\s+/', ' ', $smartyOutput)), trim(preg_replace('/\s+/', ' ', $sprigOutput)));
    }
    

    function templateFiles() {
        $files = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__) . "/" . self::TEMPLATE_DIR), RecursiveIteratorIterator::LEAVES_ONLY), '/\.tpl$/');
        $ret = array_values(array_map(create_function('$f', 'return array(preg_replace(\'~.*/assets/integration/~\', \'\', (string)$f));'), iterator_to_array($files)));
        return $ret;
    }
    
}