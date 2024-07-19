<?php
namespace axenox\SurveyPrinter\Interfaces;

interface RendererInterface
{
    /**
     * Renders a JSON part into corresponding HTML element.
     *
     * @param array $jsonPart
     * @param array $answerJson
     * @return string
     */
    public function render(array $jsonPart, array $answerJson) : string;
}