<?php
namespace axenox\SurveyPrinter\Interfaces;

interface RendererInterface
{
	/**
	 * Renders a JSON part into corresponding HTML element.
	 * 
	 * @return string
	 */
    public function render(array $jsonPart, array $awnserJson) : string;
}