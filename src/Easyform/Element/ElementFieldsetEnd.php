<?php
namespace Easyform;

class ElementFieldsetStart extends Element
{
    public function render()
    {
        $output  = '<fieldset';
        $output .= $this->renderAllAttributes();
        $output .= '>' . "\n";
        if ($this->legend != '') {
             $output .= '    <legend>' . $this->legend . '</legend>' . "\n";
        }

        return $output;
    }
}
