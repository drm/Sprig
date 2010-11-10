<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

/**
 * @property Smarty $smarty
 * @property Sprig_Environment $sprig
 * @property Twig_Environment $twig
 * @property array $testData
 * @property string $templateDir
 */

class SmartyIntegrationTest extends PHPUnit_Framework_TestCase {
    const TEMPLATE_DIR = '../assets/integration';
    function setUp() {
        if(!class_exists('Smarty', true)) {
            $this->markTestSkipped("Need Smarty class to test smarty integration!");
        }

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
                array('bar1' =>  str_repeat(chr(rand(65, 65+27)), 100)),
                array('bar2' =>  str_repeat(chr(rand(65, 65+27)), 100)),
                array('bar3' =>  str_repeat(chr(rand(65, 65+27)), 100))
            ),
            'var' => str_repeat(chr(rand(65, 65+27)), 100)
        );
        $this->smarty = new Smarty();
        $this->smarty->template_dir = self::TEMPLATE_DIR;
        $this->smarty->compile_dir = $tmpDir;
        $this->smarty->template_dir = dirname(__FILE__) . "/" . self::TEMPLATE_DIR;
        $this->smarty->config_load(dirname(__FILE__) . "/" . self::TEMPLATE_DIR . '/vars.conf');

        $this->testData['_config'] = $this->smarty->_config[0]['vars'];

        $this->sprig = new Sprig_Environment(
            new Twig_Loader_Filesystem(dirname(__FILE__) . "/" . self::TEMPLATE_DIR),
            array(
                'cache' => $tmpDir,
                'debug' => true
            )
        );
        $this->sprig->addExtension(new Sprig_Extension_Smarty());

        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem(dirname(__FILE__) . "/" . self::TEMPLATE_DIR),
            array(
                'cache' => $tmpDir,
                'debug' => true
            )
        );
        
        $this->compatSprig = new Sprig_Environment(
            new Twig_Loader_Filesystem(dirname(__FILE__) . "/" . self::TEMPLATE_DIR),
            array(
                'cache' => $tmpDir,
                'debug' => true
            )
        );
        $this->compatSprig->getLexer()->setDelimiters('comment', '{#', '#}');
        $this->compatSprig->getLexer()->setDelimiters('var', '{{', '}}');
        $this->compatSprig->getLexer()->setDelimiters('block', '{%', '%}');
    }


    /**
     * @dataProvider templateFiles
     */
    function testSmartyAndSprigRenderEquivalent($file) {
        $this->smarty->assign($this->testData);
        $this->assertOutputIsEquivalent($this->smarty->fetch($file), $this->sprig->loadTemplate($file)->render($this->testData));
    }


    /**
     * @dataProvider templateFiles
     */
    function testSprigAndTwigRenderEquivalent ($file) {
        if(!is_file(dirname(__FILE__) . '/' . self::TEMPLATE_DIR . '/' . "$file.twig")) {
            $this->markTestSkipped("Template $file.twig does not exist");
        }
        $this->assertOutputIsEquivalent(
            $this->twig->loadTemplate("$file.twig")->render($this->testData),
            $this->sprig->loadTemplate($file)->render($this->testData)
        );
    }

    /**
     * @dataProvider templateFiles
     */
    function testTwigAndCompatSprigRenderEquivalent ($file) {
        if(!is_file(dirname(__FILE__) . '/' . self::TEMPLATE_DIR . '/' . "$file.twig")) {
            $this->markTestSkipped("Template $file.twig does not exist");
        }
        $this->assertOutputIsEquivalent(
            $this->twig->loadTemplate("$file.twig")->render($this->testData),
            $this->compatSprig->loadTemplate("$file.twig")->render($this->testData)
        );
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
