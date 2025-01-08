<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class PanelRenderer extends AbstractRenderer
{	
    /**
     * This renderer is a strict renderer for panels of a SurveyJs.
	 * The SurveyJs has to have an array ´elements´ that contains all related elements.
	 * 
	 * The answer json will be contributed to all inner elements.
	 * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
	public function render(array $jsonPart, array $answerJson) : string
	{
		$attributes = $this->renderAttributesToRender($jsonPart);
    	$renderedElements = $this->renderElements($jsonPart, $answerJson);
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
    	//elements should be on the next level
    	$this->resolver->increaseLevel();
        $html = '';
    	foreach ($jsonPart['elements'] as $el) {
    		// skip expressions in export
    		if (array_key_exists('type', $el) && $el['type'] === 'expression') {
    			continue;
    		} else {
    			$html .= $this->resolveElement($el, $answerJson);
    		}
    	}
    	$this->resolver->decreaseLevel();
    	
    	return $html;
    }

    
    
}