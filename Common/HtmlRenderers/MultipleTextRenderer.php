<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class MultipleTextRenderer extends AbstractRenderer
{	
    /**
 	 * The MultipleTextRenderer is a strict renderer for the multiple text element of SurveyJs.
	 * The SurveyJs has to have an array ´items´ that contains only the ´name´ of the question.
	 * This means only the TextRenderer can be used for its child elements.
	 * 
	 * The answer json will be contributed to all inner text elements and looks like this:
 	 * "multiTextName": {
	 *	 "textName1": "Test2",
	 *	 "textName2": "Test2"
	 * }
	 * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
	public function render(array $jsonPart, array $answerJson) : string
    {        
    	$renderedElements = $this->renderElements($jsonPart, $answerJson[$jsonPart['name']]);
    	if ($renderedElements === ''){
    		return '';
    	}
    	
        return <<<HTML
    <label class='form-panelTitle'>{$this->translateElement($jsonPart['title'])}</label>
	<div class='form-items'>
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
    	$html = '';
    	foreach ($jsonPart['items'] as $el) {
    		$html .= (new TextRenderer())->render($el, $answerJson);
    	}
    	
    	return $html;
    }

    
    
}