<?php
namespace Easyform;

use Easyform\Element;

class Text extends Element
{
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<input type="text"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        $output .= '</div>' . "\n";

        return $output;
    }
}
