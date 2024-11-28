<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class DynamicPanelRenderer extends AbstractRenderer
{	
    /**
     * This renderer is a strict renderer for panels of a SurveyJs.
	 * The SurveyJs has to have an array ´templateElements´ that contains all related elements.
	 * 
	 * The answer json for this panel is related to the panel with an inner layer to pass for the other elements.
	 * ´"dynamicPanelName": [
	 *	{
	 *		"elementName": "Text",
	 *		"elementName2": true,
	 *	},
	 *	{
	 *		"elementName": "Text2",
	 *		"elementName2": false,
	 *	}
	 * ]´
	 * (!) The elementNames in the dynamic panel will be equal but separate objects in the array.
	 * 
 	 * @author miriam.seitz
     */
	public function render(array $jsonPart, array $answerJson) : string
    {    	
    	$attributes = $this->renderAttributesToRender($jsonPart);
    	$renderedElements = $this->renderElements($jsonPart, $answerJson[$jsonPart['name']]);
    	if ($renderedElements === ''){
    		return '';
    	}
    	
        return <<<HTML
        
	<div class='form-panel'>
		{$attributes}
		{$renderedElements}
	</div>
HTML;
    }


    /**
     *
     * @param array $jsonPart
     * @param array $answerJson
     * @return string
     */
    public function renderElements(array $jsonPart, array $answerJson) : string
    {
    	// Elements should be on the next level.
    	$this->resolver->increaseLevel();
    	// Multiple panels with answer.
        $html = '';
	    foreach($answerJson as $entry) {
			foreach ($jsonPart['templateElements'] as $element) {
				// Element is an array
				if (is_array($element)) {
					foreach ($element as $jsonObject) {
						$htmlElement = $this->resolver->findRenderer($jsonObject)->render($jsonObject, $entry);
						$html .= $htmlElement;
					}
					continue;
				}
				// Skip expressions in export
				if (array_key_exists('type', $element) && $element['type'] === 'expression') {
					continue;
				} else {
					$htmlElement = $this->resolver->findRenderer($element)->render($element, $entry);
					$html .= $htmlElement;
				}
			}
	    }
	    $this->resolver->decreaseLevel();
	    
    	return $html;
    }    
}