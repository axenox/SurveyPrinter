<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The BooleanRenderer is for simple yes or no questions.
 * 
 * The element of the SurveyJs should not contain any predefined awnsers
 * and the awnser json should contain true or false under the name of the related question:
 * ´"NameOfTheQuestion": true,´
 * 
 * @author miriam.seitz
 *
 */
class BooleanRenderer extends QuestionRenderer
{	
    /**
     * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
	public function render(array $jsonPart, array $awnserJson) : string
	{
		if ($this->isPartOfAwnserJson($jsonPart, $awnserJson) === false) {
			return '';
		}
		
    	$awnser = $awnserJson[$jsonPart['name']];
    	$awnser = $awnser === true ? 'Ja' : 'Nein';
		return <<<HTML
		
	<div class='form-text'>
		<label>{$jsonPart['title']}</label>
		<span class='form-value'>{$awnser}</span>
		<span class='form-description'>{$jsonPart['description']}</label>
	</div>
HTML;
    }
}