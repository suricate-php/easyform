<?php
namespace EasyForm;

class Element
{
    public $objectHtmlProperties = array(
                                    'type',
                                    'label',
                                    'name',
                                    'id',
                                    'class',
                                    'value',
                                    'checked',
                                    'rows',
                                    'cols',
                                    'possibleValues',
                                    'placeholder',
                                    'tabindex',
                                    'accesskey',
                                    'disabled',
                                    'spellcheck',
                                    'events',
                                    'helpInline',
                                    'helpBlock',
                                    'legend',
                                    'multiple',
                                    'autocomplete'
                                );
    public $objectHtmlValues          = array();

    public $objectOptionsProperties  = array(
                                        'repeat',
                                        'isDatasourceProperty',
                                        'subItemName',
                                        'subItemClass',
                                        'addEmptyEntry',
                                        'customContent',
                                        'useOffsetAsField'
                                    );
    public $objectOptionsValues = array();

    public $objectRuntimesProperties = array(
                                        'repeatOffset',
                                        'subItemOffset'
                                    );

    public $objectRuntimesValues     = array();

    public function __construct($item_data)
    {
        // Default values
        $this->repeat               = 1;
        $this->isDatasourceProperty = false;
        $this->subItemName          = '';

        $this->repeatOffset         = 0;
        $this->subItemOffset        = 0;

        foreach ($item_data as $itemProperty => $itemValue) {
            $this->$itemProperty = $itemValue;
        }

        // Check data coherence (repeat with sub_item_name etc)
    }

    /**
    * Getter
    * $name : string, name of the variable to get
    **/
    public function __get($name)
    {
        if (isset($this->objectHtmlValues[$name])) {
            return $this->objectHtmlValues[$name];
        } elseif (isset($this->objectOptionsValues[$name])) {
            return $this->objectOptionsValues[$name];
        } elseif (isset($this->objectRuntimesValues[$name])) {
            return $this->objectRuntimesValues[$name];
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->objectHtmlProperties)) {
            $this->objectHtmlValues[$name] = $value;
        } elseif (in_array($name, $this->objectOptionsProperties)) {
            $this->objectOptionsValues[$name] = $value;
        } elseif (in_array($name, $this->objectRuntimesProperties)) {
            $this->objectRuntimesValues[$name] = $value;
        }
    }

    public function __isset($name)
    {
        return in_array($name, $this->objectHtmlProperties) || isset($name, $this->objectOptionsProperties) || in_array($name, $this->objectRuntimesProperties);
    }

    public function render()
    {

    }

    protected function renderLabel()
    {
        $output = '';
        if ($this->label != '' && $this->subItemOffset == 0) {
            $output .= '<label class="control-label"';
            if ($this->id != '') {
                $output .= ' for="' . htmlentities($this->id, ENT_COMPAT, EasyForm::$formEncoding) . '"';
            }
            $output .= '>';
            $output .= $this->label;
            $output .= '</label>'."\n";
        }

        return $output;
    }

    protected function renderHelp()
    {
        $output = '';
        if ($this->helpInline != '') {
            $output .= '<span class="help-inline">' . $this->helpInline . '</span>';
        }

        if ($this->helpBlock != '') {
            $output .= '<p class="help-block">' . $this->helpBlock . '</p>';
        }

        return $output;
    }

    protected function renderAllAttributes()
    {
        $output  = $this->renderAttribute('id');
        $output .= $this->renderAttribute('name');
        $output .= $this->renderAttribute('class');
        $output .= $this->renderAttribute('rows');
        $output .= $this->renderAttribute('cols');
        $output .= $this->renderAttribute('tabindex');
        $output .= $this->renderAttribute('accesskey');
        $output .= $this->renderAttribute('disabled');
        $output .= $this->renderAttribute('checked');
        $output .= $this->renderAttribute('placeholder');
        $output .= $this->renderAttribute('spellcheck');
        $output .= $this->renderAttribute('multiple');
        $output .= $this->renderAttribute('autocomplete');

        if (is_array($this->events)) {
            foreach ($this->events as $eventName => $eventData) {
                $output .= $this->renderEvent($eventName, $eventData);
            }
        }

        return $output;
    }

    private function renderAttribute($attribute)
    {
        $output = '';

        if ($this->$attribute != '') {
            if ($attribute == 'name') {
                $suffix = ($this->repeat > 1) ? '[]' : '';
                if (is_array($this->subItemName)) {
                    $value = $this->$attribute . '[' . $this->subItemName[$this->subItemOffset] . ']' . $suffix;
                } else {
                    $value = $this->$attribute . $suffix;
                }
            } else {
                $value = $this->$attribute;
            }

            $output .= ' ' . $attribute . '="' . htmlentities($value, ENT_COMPAT, EasyForm::$formEncoding) . '"';
        }

        return $output;
    }

    private function renderEvent($event_name, $event_data)
    {
        $output = '';

        if ($event_data != '') {
            $output .= ' '  . $event_name . '="' . $event_data . '"';
        }

        return $output;
    }

    protected function renderValue($no_tag = false)
    {
        $output  = '';

        // TODO : gérer les optgroup
        if ($no_tag) {
            $output .= htmlentities($this->value, ENT_COMPAT, EasyForm::$formEncoding);
        } else {
            // Multiple possible values, mainly for SELECT
            if (isset($this->possibleValues) && is_array($this->possibleValues)) {
                // Toujours un array non ?
                if (is_array($this->subItemName)) {
                    $subItemName = $this->subItemName[$this->subItemOffset];

                    if (is_array($this->value[$subItemName])) {
                        if (isset($this->value[$subItemName][$this->repeatOffset])) {
                            $tmpValue = $this->value[$subItemName][$this->repeatOffset];

                        } else {
                            $tmpValue = '';
                        }
                    } else {
                        $tmpValue = $this->value[$subItemName];
                    }
                } else {
                    $tmpValue = $this->value;
                }
                if ($this->addEmptyEntry) {
                    $output .= '<option value="">-</option>'."\n";
                }

                foreach ($this->possibleValues as $key => $val) {
                    $selected = ( $tmpValue == $key ) ? 'selected' : '';
                    $output .= '<option value="' . htmlentities($key, ENT_COMPAT, EasyForm::$formEncoding) . '"' . $selected . '>' . htmlentities($val, ENT_COMPAT, EasyForm::$formEncoding) . '</option>'."\n";
                }

            } else {
                // We're using a Collection / CollectionMapping
                if (is_array($this->subItemName)) {
                    $subItemName = $this->subItemName[$this->subItemOffset];

                    if (is_array($this->value[$subItemName])) {
                        if (isset($this->value[$subItemName][$this->repeatOffset])) {
                            $value = $this->value[$subItemName][$this->repeatOffset];
                        } else {
                            $value = '';
                        }
                    } else {
                        $value = $this->value[$subItemName];
                    }
                } else {
                    if (is_array($this->value)) {
                        $value = $this->value[$this->repeatOffset];
                    } elseif ($this->type == EasyForm::INPUT_CHECK) {
                        $value = $this->possibleValues;
                    } else {
                        $value = $this->value;
                    }
                }
                $output .= ' value="' . htmlentities($value, ENT_COMPAT, EasyForm::$formEncoding) . '"';
            }
        }

        return $output;
    }
}
