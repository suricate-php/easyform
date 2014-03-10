<?php
namespace EasyForm;

class ElementHidden extends Element
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
