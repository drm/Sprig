<?php
/**
 * @author Gerard van Helden <drm@melp.nl>
 */

class Sprig_Node_Smarty_Section extends Sprig_Node_Smarty
{
    function __construct($tagName, $parameters, $body, $else, $lineNo)
    {
        parent::__construct($tagName, $parameters, $body, $lineNo);
        $this->setNode('else', $else);
    }


    public function compile($compiler)
    {
        $compiler
                ->addDebugInfo($this);

        if ($this->getParameter('name') instanceof Twig_Node_Expression_Constant) {
            $itemName = $this->getParameter('name')->getAttribute('value');
        } else {
            throw new Sprig_SyntaxError('missing name attribute', $this->attributes['name']->getLine());
        }

        //      $output .= "unset(\$this->_sections[$section_name]);\n";
        $compiler->write('unset($context[\'smarty\'][\'sections\'][\'' . $itemName . '\']);' . "\n");

        //      $section_props = "\$this->_sections[$section_name]";
        $sectionContainer = '$context[\'smarty\'][\'section\'][\'' . $itemName . '\']';


        //      foreach ($attrs as $attr_name => $attr_value) {
        foreach ($this->getNode('parameters') as $attrName => $attrValue) {
            switch ($attrName) {
                case 'loop':
//                        $output .= "{$section_props}['loop'] = is_array(\$_loop=$attr_value) ? count(\$_loop) : max(0, (int)\$_loop); unset(\$_loop);\n";
                    $compiler
                            ->write('$_loop=')->subcompile($attrValue)->raw(";\n")
                            ->write('if(is_array($_loop)) {' . "\n")
                            ->indent()
                            ->write($sectionContainer . '[\'loop\'] = count($_loop);' . "\n")
                            ->outdent()
                            ->write("} else {\n")
                            ->indent()
                            ->write($sectionContainer . '[\'loop\'] = max(0, (int) $_loop);' . "\n")
                            ->outdent()
                            ->write("}\n")
                            ->write('unset($_loop);' . "\n");
                    ;
                    break;

                case 'show':
//                        if (is_bool($attr_value))
//                            $show_attr_value = $attr_value ? 'true' : 'false';
//                        else
//                            $show_attr_value = "(bool)$attr_value";
//                        $output .= "{$section_props}['show'] = $show_attr_value;\n";
                    $compiler
                            ->write($sectionContainer . '[\'show\'] = (bool) ')
                            ->subcompile($attrValue)
                            ->raw(";\n");
                    break;

                case 'name':
                    $compiler
                            ->write($sectionContainer . '[\'name\'] = ')->subcompile($attrValue)->raw(";\n");
                    break;

                case 'max':
                case 'start':
                    $compiler->write($sectionContainer . '[\'' . $attrName . '\'] = (int) ')->subcompile($attrValue)->raw(";\n");
                    break;

                case 'step':
                    $compiler->write('$_step = (int) ')->subcompile($attrValue)->raw(";\n");
                    $compiler
                            ->write('if($_step == 0) {' . "\n")
                            ->indent()
                            ->write($sectionContainer . '[\'step\']= 1;' . "\n")
                            ->outdent()
                            ->write('} else {' . "\n")
                            ->indent()
                            ->write($sectionContainer . '[\'step\']= $_step;' . "\n")
                            ->outdent()
                            ->write("}\n");
                    ;
                    break;

                default:
                    throw new Sprig_SyntaxError("unknown section attribute - '$attrName'", $attrValue->getLine());
                    break;
            }
        }

        if (!$this->hasParameter('show'))
            $compiler->write($sectionContainer . '[\'show\'] = true;' . "\n");

        if (!$this->hasParameter('loop'))
            $compiler->write($sectionContainer . '[\'loop\'] = 1;' . "\n");

        if (!$this->hasParameter('max'))
            $compiler->write($sectionContainer . '[\'max\'] = ' . $sectionContainer . '[\'loop\'];' . "\n");
        else
            $compiler
                    ->write('if(' . $sectionContainer . '[\'max\'] < 0) {' . "\n")
                    ->indent()
                    ->write($sectionContainer . '[\'max\'] = ' . $sectionContainer . '[\'loop\'];' . "\n")
                    ->outdent()
                    ->write("}\n");

        if (!$this->hasParameter('step'))
            $compiler->write($sectionContainer . '[\'step\'] = 1;' . "\n");

        if (!$this->hasParameter('start'))
            $compiler->write($sectionContainer . '[\'start\'] = ' . $sectionContainer . '[\'step\'] > 0 ? 0 : ' . $sectionContainer . '[\'loop\'] -1; ' . "\n");
        else {
            $compiler
                    ->write('if(' . $sectionContainer . '[\'start\'] < 0) {' . "\n")
                    ->indent()
                    ->write($sectionContainer . '[\'start\'] = ')
                    ->raw('max(')
                    ->raw($sectionContainer . '[\'step\'] > 0 ? 0 : -1, ')
                    ->raw($sectionContainer . '[\'loop\'] + ' . $sectionContainer . '[\'start\']')
                    ->raw(');' . "\n")
                    ->outdent()
                    ->write('} else {' . "\n")
                    ->indent()
                    ->write($sectionContainer . '[\'start\'] = ')
                    ->raw('min(')
                    ->raw($sectionContainer . '[\'start\'], ')
                    ->raw($sectionContainer . '[\'step\'] > 0 ? ' . $sectionContainer . '[\'loop\'] : ' . $sectionContainer . '[\'loop\'] -1')
                    ->raw(');' . "\n")
                    ->outdent()
                    ->write("}\n");
        }

        $compiler
                ->write('if(' . $sectionContainer . '[\'show\']) {' . "\n")
                ->indent();

        if (!$this->hasParameter('start') && !$this->hasParameter('step') && !$this->hasParameter('max')) {
            $compiler->write($sectionContainer . '[\'total\'] = ' . $sectionContainer . '[\'loop\'];' . "\n");
        } else {
            $compiler
                    ->write($sectionContainer . '[\'total\'] = ')
                    ->raw('min(')
                    ->raw('ceil(')
                    ->raw('(' . $sectionContainer . '[\'step\'] > 0 ')
                    ->raw('? ' . $sectionContainer . '[\'loop\'] - ' . $sectionContainer . '[\'start\'] ')
                    ->raw(': ' . $sectionContainer . '[\'start\'] +1')
                    ->raw(')/abs(' . $sectionContainer . '[\'step\'])')
                    ->raw('), ')
                    ->raw($sectionContainer . '[\'max\']')
                    ->raw(");\n");
        }
        $compiler
                ->write('if(' . $sectionContainer . '[\'total\'] == 0) {' . "\n")
                ->indent()
                ->write($sectionContainer . '[\'show\'] = false;' . "\n")
                ->outdent()
                ->write('}' . "\n")
                ->outdent();

        $compiler
                ->write('} else {' . "\n")
                ->indent()
                ->write($sectionContainer . '[\'total\'] = 0;' . "\n")
                ->outdent()
                ->write('}' . "\n");

        //            $output .= "
        //                for ({$section_props}['index'] = {$section_props}['start'], {$section_props}['iteration'] = 1;
        //                     {$section_props}['iteration'] <= {$section_props}['total'];
        //                     {$section_props}['index'] += {$section_props}['step'], {$section_props}['iteration']++):\n";
        //            $output .= "{$section_props}['rownum'] = {$section_props}['iteration'];\n";
        //            $output .= "{$section_props}['index_prev'] = {$section_props}['index'] - {$section_props}['step'];\n";
        //            $output .= "{$section_props}['index_next'] = {$section_props}['index'] + {$section_props}['step'];\n";
        //            $output .= "{$section_props}['first']      = ({$section_props}['iteration'] == 1);\n";
        //            $output .= "{$section_props}['last']       = ({$section_props}['iteration'] == {$section_props}['total']);\n";

        $compiler
                ->write('if(' . $sectionContainer . '[\'show\']) {' . "\n")
                ->indent()
                ->write('for(' . "\n")
                ->indent(2)
                ->write($sectionContainer . '[\'index\'] = ' . $sectionContainer . '[\'start\'], ' . $sectionContainer . '[\'iteration\'] = 1;' . "\n")
                ->write($sectionContainer . '[\'iteration\'] <= ' . $sectionContainer . '[\'total\'];' . "\n")
                ->write($sectionContainer . '[\'index\'] += ' . $sectionContainer . '[\'step\'], ' . $sectionContainer . '[\'iteration\'] ++' . "\n")
                ->outdent(2)
                ->write(') {' . "\n")
                ->indent()
                ->write("{$sectionContainer}['rownum']      = {$sectionContainer}['iteration'];\n")
                ->write("{$sectionContainer}['index_prev']  = {$sectionContainer}['index'] - {$sectionContainer}['step'];\n")
                ->write("{$sectionContainer}['index_next']  = {$sectionContainer}['index'] + {$sectionContainer}['step'];\n")
                ->write("{$sectionContainer}['first']       = ({$sectionContainer}['iteration'] == 1);\n")
                ->write("{$sectionContainer}['last']        = ({$sectionContainer}['iteration'] == {$sectionContainer}['total']);\n")
                ->subcompile($this->getNode('body'))
                ->outdent()
                ->write('} // for' . "\n")
                ->outdent();


        if ($this->hasNode('else') && null !== $this->getNode('else')) {
            $compiler
                    ->write('} else {' . "\n")
                    ->indent()
                    ->subcompile($this->getNode('else'))
                    ->outdent()
                    ->write('}' . "\n");
        } else {
            $compiler->write('}' . "\n");
        }
    }
}