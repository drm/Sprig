<?php

class Sprig_Node_Smarty_ParameterList extends Twig_Node
{
    public function compile($compiler)
    {
        $compiler
                ->write('array(')
                ->raw("\n")
                ->indent();
        $i = 0;
        foreach ($this->nodes as $name => $expression) {
            if ($i++ > 0) {
                $compiler->raw(",\n");
            }
            $compiler
                    ->write("")->repr($name)->raw(' => ')
                    ->subcompile($expression);
        }

        $compiler->outdent()->write(")\n");
    }

}