<?php
namespace Easyform;

use Easyform\Element;

class FieldsetEnd extends Element
{
    public function render()
    {
        $output  = '</fieldset>';

        return $output;
    }
}
