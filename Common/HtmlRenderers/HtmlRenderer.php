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
    	if (($html = $jsonPart['html']) !== null) {
            // TODO geb 2024-11-28: This may be an array, because the form offers localization options (i.e. ['de' => html, 'en' => html, ...])
            // TODO We don't really use this feature at the moment, so we are just using the first option, hoping that it is the default.
            if(is_array($html)) {
                $html = $html[array_key_first($html)];
            }
    	    return <<<html
    	    <div style='page-break-after: avoid; page-break-inside: avoid;'>{$html}</div>
html;
    	}
    		
    	return '';
    }
}