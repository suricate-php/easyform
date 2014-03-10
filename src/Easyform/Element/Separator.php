<?php
namespace Easyform\Element;

use Easyform\Element;

class Separator extends Element
{
    public function render()
    {
        $output  = '<hr/>';

        return $output;
    }
}
