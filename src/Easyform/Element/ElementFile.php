<?php
namespace Easyform;

class ElementFile extends Element
{
    public function __construct($item_data)
    {
        $this->objectOptionsProperties[] = 'dragDrop';
        $this->objectOptionsProperties[] = 'filePreview';
        $this->objectOptionsProperties[] = 'ajaxUpload';
        
        parent::__construct($item_data);
    }
    public function render()
    {
        if ($this->filePreview) {
            $eventCall = 'handleFileSelect(event, \'' . $this->name . '\', \'URL\', \'container-' . $this->name . '\', \'' . $this->ajaxUpload . '\')';
            $events = $this->events;
            if (!is_array($events)) {
                $events = array();
            }
             $events['onchange'] = $eventCall;
            
            $this->events = $events;
        }

        $output  = $this->renderLabel();
        $output .= '<div class="controls">' . "\n";
        $output .= '<input type="file"';
        $output .= $this->renderAllAttributes();
        $output .= $this->renderValue();
        $output .= ' />';
        $output .= $this->renderHelp();
        

        if ($this->dragDrop) {
            $output .= '<div id="dropzone-' . $this->name . '" class="easyform-dropzone">Déposez les fichiers ici</div>' . "\n";
            $output .= '<script type="text/javascript">'."\n";
            $output .= '    $("#dropzone-' . $this->name . '").bind("dragover", handleDragOver);'."\n";
            $output .= '    $("#dropzone-' . $this->name . '").bind("drop", function (event) {handleFileDrop(event, \'' . $this->name . '\', \'URL\', \'container-' . $this->name . '\', \'' . $this->ajaxUpload . '\')});'."\n";
            $output .= '</script>'."\n";
        }
        if ($this->filePreview) {
            $output .= '<div class="easyform-upload-preview" id="container-' . $this->name . '">' . "\n";
            $output .= '    <div class="toolbar">' . "\n";

            $output .= '    </div>' . "\n";
            $output .= '    <div class="progress progress-striped active">' . "\n";
            $output .= '        <div class="bar"></div>' . "\n";
            $output .= '    </div>' . "\n";
            $output .= '</div>' . "\n";
        }
    
        $output .= '</div>' . "\n";
        return $output;
    }
}
