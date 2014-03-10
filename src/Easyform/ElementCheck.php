<?php
namespace Easyform;

class ElementCheck extends Element
{
    public function __construct($item_data)
    {
        parent::__construct($item_data);
        if (isset($this->possibleValues)) {
            if ($this->value == $this->possibleValues && $this->checked !== false) {
                $this->checked = "true";
            }
        }
    }

    public function render()
    {
        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<input type="checkbox"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        $output .= '</div>' . "\n";
        
        return $output;
    }
}
