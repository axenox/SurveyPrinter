<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;
use axenox\SurveyPrinter\Interfaces\RendererResolverInterface;

/**
 * This is an abstract class used if the renderer has elements and needs an resolver to find the right renderer for them.
 * 
 * @author miriam.seitz
 *
 */
abstract class AbstractRenderer implements RendererInterface
{
	protected RendererResolverInterface $resolver;

    /**
     *
     * @param RendererResolverInterface $resolver
     */
	public function __construct(RendererResolverInterface $resolver)
	{
		$this->resolver = $resolver;
	}
	
	protected function renderAttributesToRender(array $jsonPart) : string
	{
		$html = '';
		if ($jsonPart['title'] !== null){
			$html .= $this->createHeading($jsonPart);
		}
		if ($jsonPart['description'] !== null){
			$html .= <<<HTML
    		<div class='form-description'>{$jsonPart['description']}</div>
 HTML;
		}
		
		return $html;
	}
	
	protected function createHeading(array $jsonPart) : string
	{
		if ($jsonPart['showTitle'] === false){
			return '';
		}
		
		switch (true){
			case $jsonPart['type'] === 'panel':
			case $jsonPart['type'] === 'paneldynamic':
				$cssClass = 'form-panel-title';
				break;
			default:
				$cssClass = 'form-page-title';
		}
		return <<<HTML
    		<h{$this->resolver->getLevel()} class='{$cssClass}' style='page-break-after: avoid;'>{$jsonPart['title']}</h{$this->resolver->getLevel()}>
 HTML;
	}
}