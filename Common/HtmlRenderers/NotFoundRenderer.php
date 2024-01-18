<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

class NotFoundRenderer implements RendererInterface
{	
    /**
     * This renderer is used to print a hint on the HTML that explains that the format could not be rendered.
     * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
    public function render(array $jsonPart, array $awnserJson): string
    {
    	return <<<HTML
    	
	<div class='form-text'>Could not resolve content. Please contact support.</div>
HTML;
    }
}