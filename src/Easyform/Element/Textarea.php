<?php
namespace Easyform\Element;

use Easyform\Element;

class Textarea extends Element
{
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<textarea';
        $output .= $this->renderAllAttributes();
        $output .= ' >';
        $output .= $this->renderValue(true);
        $output .= '</textarea>';
        $output .= $this->renderHelp();
        $output .= '</div>' . "\n";
        return $output;
    }
}
