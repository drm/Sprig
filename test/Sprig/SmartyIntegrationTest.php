<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

abstract class BaseIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $templateDir = '../assets/integration';
    protected $testData;

    function setUp()
    {
        if (is_writable(dirname(__FILE__) . '/../assets/tmp')) {
            $this->tmpDir = dirname(__FILE__) . '/../assets/tmp';
        } else {
            $this->tmpDir = sys_get_temp_dir();
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
            'var' => 'floobiedoobiedoo',
            'num' => rand(10, 100)
        );
        $this->templateDir = dirname(__FILE__) . '/' . $this->templateDir;

        $options = array(
            'cache' => $this->tmpDir,
            'debug' => true
        );
        $loader = new Twig_Loader_Filesystem(realpath($this->templateDir));
        $loader->setForceLoad(true);

        $this->engines = array();
        $this->engines['smarty'] = new Smarty();
        $this->engines['smarty']->plugins_dir[] = dirname(__FILE__) . '/../assets/plugins/';
        $this->engines['smarty']->compile_dir = $this->tmpDir;
        $this->engines['smarty']->template_dir = $this->templateDir;
        $this->engines['smarty']->config_load($this->templateDir . '/vars.conf');
        $this->engines['smarty']->config_dir = dirname(__FILE__) . '/../assets/configs/';

        $pluginLoader = new Sprig_Extension_Smarty_PluginLoader();
        $pluginLoader->addPluginDir(dirname(__FILE__) . '/../assets/plugins/');

        $this->engines['sprig'] = new Sprig_Environment(clone $loader, $options);
        $this->engines['sprig']->addExtension(new Sprig_Extension_StrictTests());
        $this->engines['sprig']->addExtension(new Sprig_Extension_Smarty());
        $this->engines['sprig']->addExtension($pluginLoader);
        $this->engines['sprig']->config_dir = dirname(__FILE__) . '/../assets/configs/';

        $this->engines['twig'] = new Twig_Environment(clone $loader, $options);
        $this->engines['twig']->addExtension(new Sprig_Extension_StrictTests());
        $this->engines['twig']->addExtension($pluginLoader);

        $this->engines['sprig_compat'] = new Sprig_Environment(clone $loader, $options);
        $this->engines['sprig_compat']->getLexer()->setDelimiters('comment', '{#', '#}');
        $this->engines['sprig_compat']->getLexer()->setDelimiters('var', '{{', '}}');
        $this->engines['sprig_compat']->getLexer()->setDelimiters('block', '{%', '%}');
        $this->engines['sprig_compat']->addExtension($pluginLoader);

        $this->testData['_config'] = $this->engines['smarty']->_config[0]['vars'];
    }


    function templateFiles()
    {
        $files = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__FILE__) . "/" . $this->templateDir . '/equivalence'), RecursiveIteratorIterator::LEAVES_ONLY), '/\.tpl$/');
        $ret = array_values(array_map(create_function('$f', 'return array(preg_replace(\'~.*/assets/integration/~\', \'\', (string)$f));'), iterator_to_array($files)));
        return $ret;
    }
}


/**
 * @property Smarty $smarty
 * @property Sprig_Environment $sprig
 * @property Twig_Environment $twig
 * @property array $testData
 * @property string $templateDir
 */

class SmartyIntegrationTest extends BaseIntegrationTest
{
    function setUp()
    {
        parent::setUp();
        if (!class_exists('Smarty', true)) {
            $this->markTestSkipped("Need Smarty class to test smarty integration!");
        }
    }


    /**
     * @dataProvider templateFiles
     */
    function testSmartyAndSprigRenderEquivalent($file)
    {
        $this->engines['smarty']->assign($this->testData);
        $this->assertOutputIsEquivalent($this->engines['smarty']->fetch($file), $this->engines['sprig']->loadTemplate($file)->render($this->testData));
    }


    /**
     * @dataProvider templateFiles
     */
    function testSprigAndTwigRenderEquivalent($file)
    {
        if (!is_file($this->templateDir . '/' . "$file.twig")) {
            $this->markTestIncomplete("Template $file.twig does not exist");
        }
        $this->assertOutputIsEquivalent(
            $this->engines['twig']->loadTemplate("$file.twig")->render($this->testData),
            $this->engines['sprig']->loadTemplate($file)->render($this->testData)
        );
    }

    /**
     * @dataProvider templateFiles
     */
    function testTwigAndCompatSprigRenderEquivalent($file)
    {
        if (!is_file($this->templateDir . '/' . "$file.twig")) {
            $this->markTestIncomplete("Template $file.twig does not exist");
        }
        $this->assertOutputIsEquivalent(
            $this->engines['twig']->loadTemplate("$file.twig")->render($this->testData),
            $this->engines['sprig_compat']->loadTemplate("$file.twig")->render($this->testData)
        );
    }


    /**
     * @dataProvider filters
     */
    function testSmartyModifiersCompatibility($pluginLoader, $filterName)
    {
        $this->engines['smarty']->assign($this->testData);
        $template = 'plugins/filters/' . $filterName . '.tpl';
        if (!is_file($this->templateDir . '/' . $template)) {
            $this->markTestIncomplete("$template does not exist");
        }
        $this->engines['sprig']->addExtension($pluginLoader);
        $this->assertOutputIsEquivalent(
            $this->engines['smarty']->fetch($template),
            $this->engines['sprig']->loadTemplate($template)->render($this->testData)
        );
    }

    /**
     * @dataProvider functions
     */
    function testSmartyFunctionsCompatibility($pluginLoader, $filterName)
    {
        $this->engines['smarty']->assign($this->testData);
        $template = 'plugins/functions/' . $filterName . '.tpl';
        if (!is_file($this->templateDir . '/' . $template)) {
            $this->markTestIncomplete("$template does not exist");
        }
        $this->engines['sprig']->addExtension($pluginLoader);
        $this->assertOutputIsEquivalent(
            $this->engines['smarty']->fetch($template),
            $this->engines['sprig']->loadTemplate($template)->render($this->testData)
        );
    }


    function filters()
    {
        $class = new ReflectionClass('Smarty');
        $pluginLoader = new Sprig_Extension_Smarty_PluginLoader(array(dirname($class->getFileName()) . '/plugins'));
        $ret = array();
        foreach (array_keys($pluginLoader->getFilters()) as $filterName) {
            $ret[] = array($pluginLoader, $filterName);
        }
        return $ret;
    }

    function functions()
    {
        $class = new ReflectionClass('Smarty');
        $pluginLoader = new Sprig_Extension_Smarty_PluginLoader(array(dirname($class->getFileName()) . '/plugins'));
        $ret = array();
        foreach ($pluginLoader->getTokenParsers() as $filterName) {
            if($filterName instanceof Sprig_Extension_Smarty_PluginLoader_FunctionTokenParser) {
                $ret[] = array($pluginLoader, $filterName->getTag());
            }
        }
        $ret[]= array($pluginLoader, 'config_load'); // special case
        return $ret;
    }


    function assertOutputIsEquivalent($expect, $actual)
    {
        $expect = trim(preg_replace('/\s+/', ' ', $expect));
        $actual = trim(preg_replace('/\s+/', ' ', $actual));
        $this->assertEquals($expect, $actual);
        if (strlen($expect) == 0 || strlen($actual) == 0) {
            $this->markTestIncomplete("The template files render empty output");
        }
    }
}
