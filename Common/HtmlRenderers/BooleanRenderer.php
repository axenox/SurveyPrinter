<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The BooleanRenderer is for simple yes or no questions.
 * 
 * The configuration differs within the SurveyJs but the awnser json 
 * If true an false has no lable there is no extra information within the SurveyJs.
 * If they have specified lables it will be configured like this:
 * "labelTrue": "itemA",
 * "labelFalse": "itemB"
 * 
 * should always contain true or false under the name of the related question:
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
		if ($awnser === true) {
			$awnser = array_key_exists('labelTrue', $jsonPart) ? $jsonPart['labelTrue'] : "Ja";
		}
		else if ($awnser === false){
			$awnser = array_key_exists('labelFalse', $jsonPart) ? $jsonPart['labelFalse'] : "Nein";
		}
		else {
			return '';
		}		
		
		return $this->renderQuestion($jsonPart, $awnser);
    }
}