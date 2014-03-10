<?php
namespace Easyform;

use Easyform\Element;

class Reset extends Element
{
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<input type="reset"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        
        return $output;
    }
}
