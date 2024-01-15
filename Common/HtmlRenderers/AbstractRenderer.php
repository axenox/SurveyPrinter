<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

abstract class AbstractRenderer implements RendererInterface
{
    protected function findRenderer($jsonPart): string
    {
        switch (true) {
            default:
                return new PageRenderer();
        }
    }
}