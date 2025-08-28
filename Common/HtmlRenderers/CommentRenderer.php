<?php

namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class CommentRenderer extends TextRenderer
{
    protected function renderQuestion(array $jsonPart, string $renderedValues) : string
    {
        $description = $this->translateElement($this->renderDescriptionIfAvailable($jsonPart));

        $additionalCssClass = match ($jsonPart['type']) {
            'text' => ' form-text',
            default => '',
        };

        return <<<HTML
    		
	<table class='form-question{$additionalCssClass}'>
		<tr>
		    <td class="form-question-label"></td>
			<td class='form-footer'>{$this->translateElement($renderedValues)}</td>
			{$description}
		</tr>
	</table>
HTML;
    }
}