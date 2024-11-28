<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

/**
 * The ChoicesRenderer should be used to render SurveyJs elements with a ´choices´ array.
 * 
 * The configuration differs for both SurveyJs and answer json.
 * If the SurveyJs only contains values and no corresponding text (like a title) the json will look like this:
 * ´"choices": [
		"item1",
		"item2",
		"item3"
	]´
 * 
 * If it does contain a text property the json will look like this:
 * ´"choices": [
 *		{
 *			"value": "item1",
 *			"text": "A"
 *		},
 *		{
 *			"value": "item2",
 *			"text": "B"
 *		},
 *		{
 *			"value": "item3",
 *			"text": "C"
 *		}
 *	]´
 * 
 * In the answer json will either be one answer:
 * ´"nameOfQuestion": "item1"´
 * or an array with multiple answer:
 * ´"nameOfQuestion": [
		"item1",
		"item2"
	],´
 * 
 * @author miriam.seitz
 *
 */
class ChoicesRenderer extends QuestionRenderer
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
    	
    	$choices = $jsonPart['choices'];
    	$answer = $answerJson[$jsonPart['name']];    
    		
    	$firstItem = true;
    	$showOtherItem = $jsonPart['showOtherItem'] ?? false;
    	$showNoneItem = $jsonPart['showNoneItem'] ?? false;
    	
    	switch (true){
            case $answer == 'other' && $showOtherItem === true:
                $otherComment = $answerJson[$jsonPart['name'] . '-Comment'];
                $values = $jsonPart['otherText'] . ($otherComment ? ' - ' . $otherComment : '');
                break;
            case $answer == 'none' && $showNoneItem === true:
                $values = '';
                break;
            default:
                $values = '';
                foreach ($choices as $choice){
                    if (is_array($choice)) {
                        $value = $this->evaluateItemWithMoreInformation($choice, $answer);
                    } else {
                        $value = $this->evaluateItem($choice,$answer);
                    }
                    
                    if ($value !== null){
                        $values .= $firstItem ? $value : ', ' . $value;
                        $firstItem = false;
                    }
                }
    	}
    	return $this->renderQuestion($jsonPart, $values);
    }
    
    /**
     * Evaluates the choice when it contains a value and text as well:
     *  {
     * 		"value": "item1",
     *  	"text": "A"
     *  }
     * 
     * @param array $choice
     * @param mixed $answer can be either an array or a string
     * @return string|NULL
     */
    protected function evaluateItemWithMoreInformation(array $choice, mixed $answer) : ?string
    {
    	if ($this->matchAnswer($choice['value'], $answer)){
    		return $choice['text'];
    	}
    	
    	return null;
    }

    /**
     * Evaluates a choice when it is only a value: "item1"
     *
     * @param string $choice
     * @param mixed  $answer can be either an array or a string
     * @return string|NULL
     */
    protected function evaluateItem(string $choice, mixed $answer) : ?string 
    {
    	if ($this->matchAnswer($choice, $answer)){
    		return $choice;
    	}
    	
    	return null;
    }
    
    protected function matchAnswer(string $choice, mixed $answer) : bool
    {
    	return is_array($answer) ? 
	    	$this->searchValueInMultipleAnswers($choice, $answer) : 
	    	$this->matchWithAnswer($choice, $answer);
    }
    
    protected function searchValueInMultipleAnswers(string $choice, array $answerArray) : bool
    {
    	return in_array($choice, $answerArray);
    }
    
    protected function matchWithAnswer(string $choice, string $answer) : bool
    {
    	return $choice === $answer;
    }
}