<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Extension_Smarty_PluginLoader_NodeVisitor implements Twig_NodeVisitorInterface
{
    private $rootNode;

    protected $plugins = array();
    protected $filters = array();
    protected $functions = array();

    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof Sprig_Node_Module) {
            $filters = $env->getFilters();
            foreach (array_unique($this->filters) as $filterName) {
                if ($filters[$filterName] instanceof Sprig_Extension_Smarty_PluginLoader_PluginInterface) {
                    $this->plugins[] = $filters[$filterName];
                }
            }
            foreach ($this->plugins as $plugin) {
                $node->addPlugin($plugin);
            }
        }
        return $node;
    }

    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Module) {
            $node = new Sprig_Node_Module($node->getNode('body'), $node->getNode('parent'), $node->getNode('blocks'), $node->getNode('macros'), $node->getAttribute('filename'));
            $this->rootNode = $node;
        } elseif ($node instanceof Twig_Node_Expression_Filter) {
            for ($i = 0; $i < count($node->getNode('filters')); $i += 2) {
                $this->filters[] = $node->getNode('filters')->getNode($i)->getAttribute('value');
            }
        } elseif ($node instanceof Sprig_Extension_Smarty_PluginLoader_PluginInterface) {
            $this->plugins[] = $node;
        }
        return $node;
    }
}