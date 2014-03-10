<?php
namespace EasyForm;

class EasyForm
{
    const INPUT_TEXT        = 1;
    const INPUT_CHECK       = 2;
    const INPUT_SELECT      = 3;
    const INPUT_RADIO       = 4;
    const INPUT_FILE        = 5;
    const INPUT_RESET       = 6;
    const INPUT_PASSWORD    = 7;
    const INPUT_IMAGE       = 8;
    const INPUT_TEXTAREA    = 9;
    const INPUT_CUSTOM      = 10;
    const SEPARATOR         = 11;

    const FIELDSET_START    = 50;
    const FIELDSET_END      = 51;

    const INPUT_HIDDEN      = 100;
    const INPUT_SUBMIT      = 101;
    const INPUT_BUTTON      = 102;

    const FORM_TYPE_CLASSIC     = 1;
    const FORM_TYPE_MULTIPART   = 2;

    const METHOD_GET            = 'GET';
    const METHOD_POST           = 'POST';

    const BUTTONSTYPE_BOTH      = 1;
    const BUTTONSTYPE_NONE      = 2;
    const BUTTONSTYPE_SUBMIT    = 3;

    static public $formEncoding = 'ISO-8859-1';

    public $items;
    public $name;
    public $formType;
    public $formCssType;
    public $class;
    public $buttonsType;

    public $datasource;

    private $formAction;
    private $formMethod;
    
    private $preSaveFunction;
    private $postSaveFunction;
    
    
    public function __construct($name, $items, &$datasource = null)
    {
        $this->name         = $name;

        // Use provided datasource, otherwise, create a dummy one
        $this->datasource         = ( $datasource == null) ? new DataSource() : $datasource;
        $this->formType           = self::FORM_TYPE_CLASSIC;
        $this->formMethod         = self::METHOD_POST;
        $this->buttonsType        = self::BUTTONSTYPE_BOTH;
        $this->formAction         = $_SERVER['REQUEST_URI'];
        $this->formCssType        = 'form-horizontal';
        
        $this->items = array();
        
        // Loop through constructing items
        foreach ($items as $currentItem) {
            
            // No default values set, try to get one from DS
            if (!isset($currentItem['value'])) {
                // Does the variable exists in DS ?
                if (isset($datasource->$currentItem['name'])) {
                    // We've got a collection, get values from all items in collection
                    if (false && is_object($datasource->$currentItem['name']) && is_subclass_of($datasource->$currentItem['name'], '\Core\Collection\Mapping')) {
                    
                    } elseif (is_object($datasource->$currentItem['name']) && is_subclass_of($datasource->$currentItem['name'], '\ArrayAccess')) {
                        // We asked for multiple items from the collection
                        if (is_array($currentItem['subItemName'])) {
                            foreach ($currentItem['subItemName'] as $subItemName) {
                                $currentItem['value'][$subItemName] = $datasource->$currentItem['name']->getValuesFor($subItemName);
                            }
                        }
                        // 2012-04-21 : toujours un array non ?
                        /* else {
                            $current_item['value'] = $datasource->$current_item['name']->getValuesFor($current_item['sub_item_name']);
                        }*/
                    } else {
                        $currentItem['value'] = $datasource->$currentItem['name'];
                    }
                } else {
                    $currentItem['value'] = null;
                }
            }
       
            if (!(isset($currentItem['displayValue'])/* && is_array($current_item['display_value'])*/)) {
                $currentItem['displayValue'] = array(
                                                        'format' => '%s',
                                                        'data' => array($currentItem['name'])
                                                    );
            }
            
            $this->items[$currentItem['name']] = $this->itemFactory($currentItem);
        }
    }

    private function itemFactory($item)
    {
        switch ($item['type']) {
            case self::INPUT_TEXT:
                $itemToAdd = new ElementText($item);
                break;
            case self::INPUT_CHECK:
                $itemToAdd = new ElementCheck($item);
                break;
            case self::INPUT_SELECT:
                if (!isset($item['possibleValues'])) {
                    if (method_exists($this->datasource, 'getPossibleValuesFor')) {
                        $item['possibleValues'] = $this->datasource->getPossibleValuesFor($item['displayValue'], true);
                    } else {
                        $item['possibleValues'] = array();
                    }
                }
                $itemToAdd = new ElementSelect($item);
                break;
            case self::INPUT_RADIO:
                $itemToAdd = new ElementRadio($item);
                break;
            case self::INPUT_FILE:
                $itemToAdd = new ElementFile($item);
                break;
            case self::INPUT_RESET:
                $itemToAdd = new ElementReset($item);
                break;
            case self::INPUT_PASSWORD:
                $itemToAdd = new ElementPassword($item);
                break;
            case self::INPUT_IMAGE:
                $itemToAdd = new ElementImage($item);
                break;
            case self::INPUT_TEXTAREA:
                $itemToAdd = new ElementTextarea($item);
                break;
            case self::INPUT_SUBMIT:
                $itemToAdd = new ElementSubmit($item);
                break;
            case self::INPUT_HIDDEN:
                $itemToAdd = new ElementHidden($item);
                break;
            case self::INPUT_BUTTON:
                $itemToAdd = new ElementButton($item);
                break;
            case self::SEPARATOR:
                $itemToAdd = new ElementSeparator($item);
                break;
            case self::FIELDSET_START:
                $itemToAdd = new ElementFieldsetStart($item);
                break;
            case self::FIELDSET_END:
                $itemToAdd = new ElementFieldsetEnd($item);
                break;
            case self::INPUT_CUSTOM:
                $itemToAdd = new ElementCustom($item);
                break;
            default:
                $itemToAdd = new ElementText($item);
                break;
        }

        return $itemToAdd;
    }
    
    public function __call($method, $args)
    {
        if ($this->{$method} instanceof \Closure) {
            return call_user_func_array($this->{$method}, $args);
        } else {
            return static::__call($method, $args);
        }
    }

    public function setEncoding($encoding)
    {
        self::$formEncoding = $encoding;
        
        return $this;
    }

    public function render()
    {
        $output = '<form action="' . $this->formAction . '" method="' . $this->formMethod . '"';
        if ($this->formType == self::FORM_TYPE_MULTIPART) {
            $output .= ' enctype="multipart/form-data"';
        }

        $output .= ' class="';
        switch ($this->formCssType) {
            case 'inline':
                $output .= 'form-inline';
                break;
            case 'search':
                $output .= 'form-search';
                break;
            case 'vertical':
                $output .= 'form-vertical';
                break;
            default:
                $output .= 'form-horizontal';
                break;
        }
        if ($this->class != '') {
            $output .= ' ' . $this->class;
        }

        $output .= '" name="' . $this->name . '">'."\n";
        
        // Loop through items, and display them
        foreach ($this->items as $currentItem) {
            $counter = 0;
            while ($counter < $currentItem->repeat) {
                $output .= '<div class="control-group">' ."\n";
                $currentItem->repeatOffset = $counter;

                if (is_array($currentItem->subItemName)) {
                    foreach ($currentItem->subItemName as $subItemKey => $subItemName) {
                        $currentItem->subItemOffset = $subItemKey;
                        if (isset($currentItem->subItemClass)
                            && isset($currentItem->subItemClass[$subItemName])) {
                            $oldClass = $currentItem->class;
                            $currentItem->class = $currentItem->subItemClass[$subItemName];
                            $output .= $currentItem->render();
                            $currentItem->class = $oldClass;
                        } else {
                            $output .= $currentItem->render();
                        }
                        

                    }
                } else {
                    $output .= $currentItem->render();
                }
                
                $output .= '</div>'."\n";
                $counter++;
            }
        }
        
        $output .= $this->renderFormButtons();
        $output .= '</form>'."\n";
        
        return $output;
    }

    private function renderFormButtons()
    {
        $output = '';
        if ($this->buttonsType != self::BUTTONSTYPE_NONE) {
            $output .= '    <div class="form-actions">' . "\n";
            $itemData = array(
                'value' => 'Valider',
                'class' => 'btn-primary'
                );
            $itemToAdd = new ElementSubmit($itemData);
            $output .= $itemToAdd->render();

            if ($this->buttonsType != self::BUTTONSTYPE_SUBMIT) {
                $output .= ' ';
                $itemData = array(
                    'value' => 'Annuler',
                    'events' => array('onclick' => 'history.go(-1);return false')
                );
                $itemToAdd = new ElementButton($itemData);
                $output .= $itemToAdd->render();
            }
            $output .= '    </div>'."\n";
        }

        return $output;
    }

    public function setMethod($method)
    {
        if ($method == self::METHOD_POST || $method == self::METHOD_GET) {
            $this->formMethod = $method;
        }
    }
    
    public function setPreSaveFunction($fct)
    {
        $this->preSaveFunction = $fct;
    }

    public function setPostSaveFunction($fct)
    {
        $this->postSaveFunction = $fct;
    }

    public function saveToDatasource()
    {
        if ($this->preSaveFunction != null) {
            $this->preSaveFunction($this->datasource);
        }
        
        $newCollection = array();
        // Loop through each form item
        foreach ($this->items as $currentFormItem) {
            $field = $currentFormItem->name;
            // Assign to datasource only if set and linked to datasource
            if ($currentFormItem->isDatasourceProperty && isset($_POST[$field])) {
                // Collection property
                if (is_array($currentFormItem->subItemName)) {
                    // Check if POST[field][]
                    if (is_array($_POST[$field])) {
                        $datasourceItem = &$this->datasource->{$field};
                        // Empty collection
                        
                        $datasourceItem->purgeItems();
                        //$collectionItemsType            = $datasourceItem->getItemsType();
                        //$collectionItemsParentIdField   = $datasourceItem->getParentIdName();

                        //echo 'Collection is composed of ' . $collection_items_type . ' with parent_id field ' . $collection_items_parent_id_field . '<br/>';
                        
                        // And add brand new items to it
                        foreach ($_POST[$field] as $subItemName => $subItemValues) {
                            // considering that a single item is an array, easier parsing
                            $subItemValues = ( !is_array($subItemValues) ) ? array($subItemValues) : $subItemValues;

                            // Get the entire collection
                            if (!isset($newCollection[$field])) {
                                $newCollection[$field] = array();
                            }
                            
                            //$new_collection[$field][$sub_item_name] = array();
                            $hasData = false;
                            foreach ($subItemValues as $subItemOffset => $subItemValue) {
                                $newCollection[$field][$subItemOffset][$subItemName] = $subItemValue;
                                $hasData |= $subItemValue != '';
                            }

                            if ($currentFormItem->useOffsetAsField != null && $hasData) {
                                $newCollection[$field][$subItemOffset][$currentFormItem->useOffsetAsField] = $subItemOffset;
                            }
                        }
                        // Build crafted collection item
                        $datasourceItem->craftItem($newCollection[$field]);

                        // Mark data as loaded
                        $this->datasource->markProtectedVariableAsLoaded($field);
                    }
                } else {
                    // echo 'Simple assignement from POST ' . $field . ' => ' . $_POST[$field] . '<br/>';
                    $this->datasource->$field = $_POST[$field];
                }
            } elseif ($currentFormItem->isDatasourceProperty) {
                // Special case for checkbox
                if ($currentFormItem->type == self::INPUT_CHECK) {
                    $this->datasource->$field = 0;
                }
            }
        }
        
        if (method_exists($this->datasource, 'save')) {
            $this->datasource->save();
        }

        if ($this->postSaveFunction != null) {
            $this->postSaveFunction($this->datasource);
        }
    }
}
