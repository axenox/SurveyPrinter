<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class PageRenderer extends AbstractRenderer
{
    /**
     * 
     * {@inheritDoc}
     * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
     */
    public function render($jsonPart): string
    {
        return '<div>' . $this->renderElements($jsonPart) . '</div>';
    }

    /**
     * 
     * @param array $jsonPart
     * @return string
     */
    public function renderElements(array $jsonPart) : string
    {
        $html = '';
        foreach ($jsonPart['elements'] as $el) {
            $html .= $this->findRenderer($el)->render($el);
        }
        return $html;
    }
}