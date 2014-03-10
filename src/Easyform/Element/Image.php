<?php
namespace Easyform\Element;

use Easyform\Element;

class Image extends Element
{
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<input type="image"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        
        return $output;
    }
}
