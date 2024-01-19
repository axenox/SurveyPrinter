<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class DynamicPanelRenderer extends AbstractRenderer
{	
    /**
     * This renderer is a strict renderer for panels of a SurveyJs.
	 * The SurveyJs has to have an array ´templateElements´ that contains all related elements.
	 * 
	 * The awnser json for this panel is related to the panel with an inner layer to pass for the other elements.
	 * 	"dynamicPanelName": [
	 *	{
	 *		"elementName": "Text",
	 *		"elementName2": true,
	 *	},
	 *	{
	 *		"elementName": "Text2",
	 *		"elementName2": false,
	 *	}
	 * ]
	 * (!) The elementNames in the dynamic panel will be equal but seperate objects in the array.
	 * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
	public function render(array $jsonPart, array $awnserJson) : string
    {
    	
    	$renderedElements =$this->renderElements($jsonPart, $awnserJson[$jsonPart['name']]);
    	if ($renderedElements === ''){
    		return '';
    	}
    	
        return <<<HTML
        
	<div class='form-panel'>
		<label class='form-panelTitle'>{$jsonPart['title']}</label>
		{$renderedElements}
	</div>
HTML;
    }

    
    /**
     *
     * @param array $jsonPart
     * @return string
     */
    public function renderElements(array $jsonPart, array $awnserJson) : string
    { 
    	// muiltiple panels with awnsers
	    foreach($awnserJson as  $entry) {
			foreach ($jsonPart['templateElements'] as $element) {
				// element is an array
				if (is_numeric($element)) {
					foreach ($element as $jsonObject) {
						$htmlElement = $this->resolver->findRenderer($jsonObject)->render($jsonObject, $entry);
						$html .= $htmlElement;
					}
					continue;
				}
				// skip expressions in export
				if (array_key_exists('type', $element) && $element['type'] === 'expression') {
					continue;
				} else {
					$htmlElement = $this->resolver->findRenderer($element)->render($element, $entry);
					$html .= $htmlElement;
				}
			}
	    }
    	return $html;
    }    
}