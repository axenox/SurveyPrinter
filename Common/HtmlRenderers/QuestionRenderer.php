<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;
use axenox\SurveyPrinter\Traits\RendererTranslatorTrait;

/**
 * The QuestionRenderer is an abstract class ensuring the context of every renderer that deals with answers.
 *
 * @author miriam.seitz
 *
 */
abstract class QuestionRenderer implements RendererInterface
{
    use RendererTranslatorTrait;
    
    protected function isPartOfAnswerJson(array $jsonPart, array $answerJson) : bool
    {
    	if ($answerJson[$jsonPart['name']] !== null) {
    		return true;
    	}
    	
    	return false;
    }
    
    protected function renderDescriptionIfAvailable(array $jsonPart) : string
    {
    	if (array_key_exists('description', $jsonPart) === false){
    		return '';
    	}
    	
    	return <<<HTML
    	<td class='form-description'>{$this->translateElement($jsonPart['description'])}</td>
HTML;
    }
    	
    protected function renderQuestion(array $jsonPart, string $renderedValues) : string
    {
    	$label = $this->translateElement($jsonPart['title'] ?? $jsonPart['name']);
    	$description = $this->translateElement($this->renderDescriptionIfAvailable($jsonPart));

        $additionalCssClass = match ($jsonPart['type']) {
            'text' => ' form-text',
            default => '',
        };
    	
    	return <<<HTML
    		
	<table class='form-question{$additionalCssClass}'>
		<tr>
			<td class="form-question-label">{$label}</td>
			<td class='form-value'>{$this->translateElement($renderedValues)}</td>
			{$description}
		</tr>
	</table>
HTML;
    }
}