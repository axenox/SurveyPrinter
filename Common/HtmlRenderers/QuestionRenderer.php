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
        // TODO: This fix is brittle and must be implemented anywhere the title attribute is read.
        // For some reason there is sometimes a `default` value and then a `de` value which actually represents what is shown as the
        // title to the user. So the logic is we take the `de` value before we take the `default`.
        // If both are empty we use the name of the jsonpart.
        // Shouldn't we also check for other languages and how do we decide which we use, maybe take the selected language from the user?
    	$title = $jsonPart['title'] ?? $jsonPart['name'];
        if(is_array($title)) {
            $label = $title['default'];
            switch (true) {
                case $title['de'] !== null && $title['de'] !== '':
                    $label = $title['de'];
                    break;
                case $title['default'] !== null && $title['default'] !== '':
                    $label = $title['default'];
                    break;
                default:
                    $label = $jsonPart['name'];
            }
        } else {
            $label = $title;
        }

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