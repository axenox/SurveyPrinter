<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

/**
 * The BooleanRenderer is for simple yes or no questions.
 * 
 * The configuration differs within the SurveyJs but the answer json 
 * If true a false has no label there is no extra information within the SurveyJs.
 * If they have specified labels it will be configured like this:
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
	public function render(array $jsonPart, array $answerJson) : string
	{
		if ($this->isPartOfAnswerJson($jsonPart, $answerJson) === false) {
			return '';
		}
		
		$answer = $answerJson[$jsonPart['name']];
		if ($answer === true) {
			$answer = array_key_exists('labelTrue', $jsonPart) ? $jsonPart['labelTrue'] : "Ja";
		}
		else if ($answer === false){
			$answer = array_key_exists('labelFalse', $jsonPart) ? $jsonPart['labelFalse'] : "Nein";
		}
		else {
			return '';
		}		
		
		return $this->renderQuestion($jsonPart, $answer);
    }
}