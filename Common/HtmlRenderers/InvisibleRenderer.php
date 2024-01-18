<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

class InvisibleRenderer implements RendererInterface
{
	/**
	 * This renderer is used if the format type is not supposed to show up in the requested HTML.
	 * 
	 * {@inheritDoc}
	 * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
	 */
	public function render(
		array $jsonPart,
		array $awnserJson): string
	{
		return '';
	}
}