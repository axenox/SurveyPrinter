<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

class SurveyRenderer extends AbstractRenderer
{
    public function render($jsonPart): string
    {
        return $this->renderElements($jsonPart);
    }

    public function renderElements(array $jsonPart) : string
    {
        $html = '';
        foreach ($jsonPart['pages'] as $el) {
            $html .= $this->findRenderer($el)->render($el);
        }
        return $html;
    }
}