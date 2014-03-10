<?php
namespace Easyform\Element;

use Easyform\Element;

class Password extends Element
{
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<input type="password"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        $output .= '</div>' . "\n";
        
        return $output;
    }
}
