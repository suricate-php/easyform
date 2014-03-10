<?php
namespace EasyForm;

class ElementButton extends Element
{
    public function __construct($item_data)
    {
        parent::__construct($item_data);
        if ($this->class != '') {
            $this->class .= ' btn';
        } else {
            $this->class = 'btn';
        }
    }

    public function render()
    {
        $output  = '<button ';
        $output .= $this->renderAllAttributes();
        $output .= '>';
        $output .= $this->renderValue(true);
        $output .= '</button>';
        $output .= $this->renderHelp();
        
        return $output;
    }
}
