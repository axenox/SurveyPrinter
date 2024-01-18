<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class PageRenderer extends AbstractRenderer
{	
	/**
	 * The PageRenderer is a strict renderer for pages of a SurveyJs.
	 * The SurveyJs has to have an array ´pages´ that contains all related elements.
	 * 
	 * The awnser json will be contributed to all inner elements.
	 * 
	 * {@inheritDoc}
	 * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
	 */
    public function render(array $jsonPart, array $awnserJson): string
    {    	
        return <<<HTML
        	
	<div class='form-page'>
		<label class='form-pageTitle'>{$jsonPart['title']}</label>
		<label class='form-description'>{$jsonPart['description']}</label>
		{$this->renderElements($jsonPart, $awnserJson)}
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