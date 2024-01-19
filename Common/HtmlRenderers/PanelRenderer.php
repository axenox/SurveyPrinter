<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class PanelRenderer extends AbstractRenderer
{	
    /**
     * This renderer is a strict renderer for panels of a SurveyJs.
	 * The SurveyJs has to have an array ´elements´ that contains all related elements.
	 * 
	 * The awnser json will be contributed to all inner elements.
	 * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
	public function render(array $jsonPart, array $awnserJson) : string
    {        
    	$renderedElements = $this->renderElements($jsonPart, $awnserJson);
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
    	$html = '';
    	foreach ($jsonPart['elements'] as $el) {
    		// element is an array
    		if (is_numeric($el)){
    			foreach ($el as $jsonElement) {
    				$html .= $this->resolver->findRenderer($jsonElement)->render($jsonElement, $awnserJson);
    			}
    		}
    		// skip expressions in export
    		if (array_key_exists('type', $el) && $el['type'] === 'expression') {
    			continue;
    		} else {
    			$html .= $this->resolver->findRenderer($el)->render($el, $awnserJson);
    		}
    	}
    	return $html;
    }

    
    
}