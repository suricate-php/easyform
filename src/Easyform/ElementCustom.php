<?php
namespace Easyform;

class ElementCustom extends Element
{
    public function render()
    {
        return str_replace('%data%', $this->value, $this->customContent);
    }
}
