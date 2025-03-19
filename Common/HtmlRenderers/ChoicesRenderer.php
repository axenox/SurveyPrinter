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
                    $value = $this->evaluateItem($choice,$answer);
                    if ($value !== null){
                        $values .= $firstItem ? $value : ', ' . $value;
                        $firstItem = false;
                    }
                }
    	}
    	return $this->renderQuestion($jsonPart, $values);
    }
    
    /**
     * Evaluate a choice.
     * 
     * @param mixed $choice
     * @param mixed $answer can be either an array or a string
     * @return string|NULL
     */
    protected function evaluateItem(mixed $choice, mixed $answer) : ?string
    {
        $value = $text = $choice;
        if(is_array($choice)) {
            $value = $choice['value'];
            $text = $choice['text'];
        }
        
        if($this->matchAnswer($value, $answer)) {
            return $this->translateElement($text);
        }
        
    	return null;
    }
    
    protected function matchAnswer(string $choice, mixed $answer) : bool
    {
    	return is_array($answer) ?
            in_array($choice, $answer) :
            $choice === $answer;
    }
}