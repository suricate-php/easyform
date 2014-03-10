<?php
namespace Easyform\Element;

use Easyform\Element;

class Custom extends Element
{
    public function render()
    {
        return str_replace('%data%', $this->value, $this->customContent);
    }
}
