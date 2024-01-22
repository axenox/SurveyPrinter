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
		$attributes = $this->renderAttributesToRender($jsonPart);
    	$renderedElements = $this->renderElements($jsonPart, $awnserJson);
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
     * @return string
     */
    public function renderElements(array $jsonPart, array $awnserJson) : string
    {
    	//elements should be on the next level
    	$this->resolver->increaseLevel();
    	foreach ($jsonPart['elements'] as $el) {
    		// element is an array
    		if (is_numeric($el)){
    			foreach ($el as $jsonElement) {
    				$html .= $this->resolver->findRenderer($jsonElement)->render($jsonElement, $awnserJson);
    			}
    			continue;
    		}
    		// skip expressions in export
    		if (array_key_exists('type', $el) && $el['type'] === 'expression') {
    			continue;
    		} else {
    			$html .= $this->resolver->findRenderer($el)->render($el, $awnserJson);
    		}
    	}
    	$this->resolver->decreaseLevel();
    	
    	return $html;
    }

    
    
}