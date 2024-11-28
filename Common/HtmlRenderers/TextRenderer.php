<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The TextRenderer is for simple questions with text output.
 * The element of the SurveyJs should not contain any predefined answers
 * and the answer json should contain the text under the name of the related question:
 * ´"NameOfQuestion": "Text",´
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
    public function render(array $jsonPart, array $answerJson): string
    {
    	if ($this->isPartOfAnswerJson($jsonPart, $answerJson) === false) {
    		return '';
    	}
    		
    	return $this->renderQuestion($jsonPart, $answerJson[$jsonPart['name']]);
    }
}