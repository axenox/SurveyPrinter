<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The HTML Renderer is for survey parts of the type html, we just return the html wrapped in a div to avoid page breake inside and after the html.
 * 
 * @author ralf.mulansky
 *
 */
class HtmlRenderer extends AbstractRenderer
{	
    /**
     * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
    public function render(array $jsonPart, array $awnserJson): string
    {
    	if ($jsonPart['html']) {
    	    return <<<html
    	    <div style='page-break-after: avoid; page-break-inside: avoid;'>{$jsonPart['html']}</div>
html;
    	}
    		
    	return '';
    }
}