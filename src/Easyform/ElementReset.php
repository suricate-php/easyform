<?php
namespace EasyForm;

class ElementReset extends Element
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
