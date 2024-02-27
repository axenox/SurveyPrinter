<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The QuestionRenderer is an abstract class ensuring the context of every renderer that deals with awnsers.
 *
 * @author miriam.seitz
 *
 */
abstract class QuestionRenderer implements RendererInterface
{	    
    protected function isPartOfAwnserJson(array $jsonPart, array $awnserJson) : bool
    {
    	if ($awnserJson[$jsonPart['name']] !== null) {
    		return true;
    	}
    	
    	return false;
    }
    
    protected function renderDescriptionIfAvailable(array $jsonPart) {
    	if (array_key_exists('description', $jsonPart) === false){
    		return '';
    	}
    	
    	return <<<HTML
    	<td class='form-description'>{$jsonPart['description']}</td>
HTML;
    }
    	
    protected function renderQuestion(array $jsonPart, string $renderedValues)
    {    		
    	$label = $jsonPart['title'] ?? $jsonPart['name'];
    	$description = $this->renderDescriptionIfAvailable($jsonPart);  
    	
    	switch ($jsonPart['type']){
    		case 'text';
    			$additionalCssClass = ' form-text';
    			break;
    	}
    	
    	return <<<HTML
    		
	<table class='form-question{$additionalCssClass}'>
		<tr>
			<td>{$label}</td>
			<td class='form-value'>{$renderedValues}</td>
			{$description}
		</tr>
	</table>
HTML;
    }
}