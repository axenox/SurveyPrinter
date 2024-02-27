<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The ChoicesRenderer should be used to render SurveyJs elements with a ´choices´ array.
 * 
 * The configuration differs for both SurveyJs and awnser json.
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
 * In the awnser json will either be one awnser:
 * ´"nameOfQuestion": "item1"´
 * or an array with multiple awnsers:
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
    public function render(array $jsonPart, array $awnserJson): string
    {
    	if ($this->isPartOfAwnserJson($jsonPart, $awnserJson) === false) {
    		return '';
    	}
    	
    	$choices = $jsonPart['choices'];
    	$awnser = $awnserJson[$jsonPart['name']];    
    		
    	$firstItem = true;
    	foreach ($choices as $choice){
    		$value = null;
    		if (is_array($choice)) {
    			$value = $this->evaluateItemWithMoreInformation($choice, $awnser);
    		} else {
    			$value = $this->evaluateItem($choice,$awnser);
    		}
    		
    		if ($value !== null){
	    		$values .= $firstItem ? $value : ', ' . $value;
	    		$firstItem = false;
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
     * @param mixed $awnser can be either an array or a string
     * @return string|NULL
     */
    protected function evaluateItemWithMoreInformation(array $choice, mixed $awnser) : ?string
    {
    	if ($this->matchAwnser($choice['value'], $awnser)){
    		return $choice['text'];
    	}
    	
    	return null;
    }
    
    /**
     * Evaluates a choice when it is only a value: "item1"
     * 
     * @param array $choice
     * @param mixed $awnser can be either an array or a string
     * @return string|NULL
     */
    protected function evaluateItem(string $choice, mixed $awnser) : ?string 
    {
    	if ($this->matchAwnser($choice, $awnser)){
    		return $choice;
    	}
    	
    	return null;
    }
    
    protected function matchAwnser(string $choice, mixed $awnser) : bool
    {
    	return is_array($awnser) ? 
	    	$this->searchValueInMultipleAwnsers($choice, $awnser) : 
	    	$this->matchWithAwnser($choice, $awnser);
    }
    
    protected function searchValueInMultipleAwnsers(string $choice, array $awnserArray) : bool
    {
    	return in_array($choice, $awnserArray);
    }
    
    protected function matchWithAwnser(string $choice, string $awnser) : bool
    {
    	return $choice === $awnser;
    }
}