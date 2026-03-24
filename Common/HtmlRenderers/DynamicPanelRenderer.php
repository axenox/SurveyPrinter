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
        
        // Prepare dynamic title rendering.
        $elementIndex = 1;
        $titleTemplate = $jsonPart['templateTitle'];
        if($titleTemplate !== null) {
            // FIXME geb 2026-03-24: We do not distinguish between ALL and VISIBLE panel index, because at this point
            // FIXME we don't have access to the unfiltered index. 
            $titleTemplate = $this->translateElement($titleTemplate);
            $titleTemplate = preg_split('/(?:{panelIndex}|{visiblePanelIndex})/', $titleTemplate);
        } else {
            $titleTemplate = false;
        }
        
        $titlePart = ['type' => 'panelDynamic'];
        
	    foreach($answerJson as $entry) {
            if($titleTemplate !== false) {
                $titlePart['title'] = implode($elementIndex, $titleTemplate);
                $html .= $this->createHeading($titlePart);
            }
            
            $elementIndex += 1;
            
			foreach ($jsonPart['templateElements'] as $element) {
				// Skip expressions in export
				if (array_key_exists('type', $element) && $element['type'] === 'expression') {
					continue;
				} else {
					$html .= $this->resolveElement($element, $entry);
				}
			}
	    }
	    $this->resolver->decreaseLevel();
	    
    	return $html;
    }
}