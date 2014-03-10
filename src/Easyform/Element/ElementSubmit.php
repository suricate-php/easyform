<?php
namespace Easyform;

class ElementSubmit extends Element
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
        $output  = '<button type="submit"';
        $output .= $this->renderAllAttributes();
        $output .= '>';
        $output .= $this->renderValue(true);
        $output .= '</button>';
        $output .= $this->renderHelp();
        
        return $output;
    }
}
