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
    	switch(true)
    	{
    		case array_key_exists('pages', $jsonPart):
    			$cssClass = 'form';
    			$elements = $jsonPart['pages'];
    			break;
    		case array_key_exists('elements', $jsonPart):
    			$cssClass = 'form-page';
    			$elements = $jsonPart['elements'];
    			break;
    	}
    	
    	$attributes = $this->renderAttributesToRender($jsonPart);
        return <<<HTML
        	
	<div class='{$cssClass}'>
		{$attributes}
		{$this->renderElements($elements, $awnserJson)}
		{$this->createFooter($jsonPart)}
	</div>
HTML;
    }

    /**
     * 
     * @param array $jsonPart
     * @return string
     */
    public function renderElements(array $elements, array $awnserJson) : string
    {        
    	$html = '';
        
        //elements should be on the next level
        $this->resolver->increaseLevel();
        foreach ($elements as $el) {
        	// element is an array
        	if (is_numeric($el)) {
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
        $this->resolver->decreaseLevel();
        
        return $html;
    }
    
    protected function createFooter(array $jsonPart) :string
    {
    	// Only one Footer for all pages
    	if (array_key_exists('pages', $jsonPart) === false){
    		return '';
    	}

        $footerContent = $this->resolver->getTranslator()->translate('FOOTER.CONTENT');
    	return <<<HTML
    	
	<div class="form-footer">
		$footerContent
	</div>
HTML;
    }
}