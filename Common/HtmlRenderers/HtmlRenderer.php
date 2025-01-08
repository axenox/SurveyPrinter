<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

/**
 * The HTML Renderer is for survey parts of the type html, we just return the html wrapped in a div to avoid page break inside and after the html.
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
    public function render(array $jsonPart, array $answerJson): string
    {
    	if (($html = $jsonPart['html']) !== null) {
            $html = $this->translateElement($html);
    	    return <<<html
    	    <div style='page-break-after: avoid; page-break-inside: avoid;'>{$html}</div>
html;
    	}
    		
    	return '';
    }
}