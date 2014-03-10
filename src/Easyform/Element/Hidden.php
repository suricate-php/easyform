<?php
namespace Easyform\Element;

use Easyform\Element;

class Hidden extends Element
{
    public function render()
    {
        $output  = '<input type="hidden"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        
        return $output;
    }
}
