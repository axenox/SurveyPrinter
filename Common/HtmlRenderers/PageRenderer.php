<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class PageRenderer extends AbstractRenderer
{	
	/**
	 * The PageRenderer is a strict renderer for pages of a SurveyJs.
	 * The SurveyJs has to have an array ´pages´ and these have to to have ojects with ´elements´ that contains all related elements.
	 * SurveyJs for all pages:
	 * "title": "Form", // optional
	 * "pages": [ .. ]
	 * 
	 * SurveyJs for one page:
	 * "name": "page1",
	 * "title": "Page" // optional 
	 * "elements": [ ... ]
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
		<h3 class='form-pageTitle'>{$jsonPart['title']}</h3>
		<div class='form-description'>{$jsonPart['description']}</div>
		{$this->renderElements($jsonPart, $awnserJson)}
		{$this->createFooter($jsonPart)}
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
        switch(true)
        {
        	case array_key_exists('pages', $jsonPart):
        		$elements = $jsonPart['pages'];
        		break;
        	case array_key_exists('elements', $jsonPart):
        		$elements = $jsonPart['elements'];
        		break;
        }
        
        foreach ($elements as $el) {
        	// element is an array
        	if (is_numeric($el)){        		
        		foreach ($el as $jsonElement) {
        			$htmlElement = $this->resolver->findRenderer($jsonElement)->render($jsonElement, $awnserJson);
        			$html .= $htmlElement;
        		}
        		continue;
        	}
        	// skip expressions in export
        	if (array_key_exists('type', $el) && $el['type'] === 'expression') {
        		continue;
        	} else {
        		$htmlElement = $this->resolver->findRenderer($el)->render($el, $awnserJson);
        		$html .= $htmlElement;
        	}
        }
        
        return $html;
    }
    
    protected function createFooter(array $jsonPart) :string
    {
    	// Only one Footer for all pages
    	if (array_key_exists('pages', $jsonPart) === false){
    		return '';
    	}
    	
    	return <<<HTML
    	
	<div class="form-footer">
		Das Formular enthält nur ausgefüllte Inhalte.
	</div>
HTML;
    }
}