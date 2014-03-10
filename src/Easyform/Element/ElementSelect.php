<?php
namespace Easyform;

class ElementSelect extends Element
{
    public function __construct($item_data)
    {
        $this->objectHtmlProperties[] = 'multiple';
        parent::__construct($item_data);
    }
    
    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<select';
        $output .= $this->renderAllAttributes();
        $output .= '>' . "\n";
        $output .= $this->renderValue();
        $output .= '</select>';
        $output .= $this->renderHelp();
        $output .= '</div>'."\n";
        
        return $output;
    }
}
