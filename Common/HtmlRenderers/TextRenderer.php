<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The TextRenderer is for simple questions with text output.
 * The element of the SurveyJs should not contain any predefined awnsers
 * and the awnser json should contain the text under the name of the related question:
 * ´"NameOfQuerstion": "Text",´
 * 
 * @author miriam.seitz
 *
 */
class TextRenderer extends QuestionRenderer
{	
    /**
     * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
    public function render(array $jsonPart, array $awnserJson): string
    {
    	if ($this->isPartOfAwnserJson($jsonPart, $awnserJson) === false) {
    		return '';
    	}
    		
    	return $this->renderQuestion($jsonPart, $awnserJson[$jsonPart['name']]);
    }
}