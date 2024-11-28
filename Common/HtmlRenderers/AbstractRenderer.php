<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;
use axenox\SurveyPrinter\Interfaces\RendererResolverInterface;
use axenox\SurveyPrinter\Traits\RendererTranslatorTrait;

/**
 * This is an abstract class used if the renderer has elements and needs a resolver to find the right renderer for
 * them.
 * 
 * @author miriam.seitz
 *
 */
abstract class AbstractRenderer implements RendererInterface
{
    use RendererTranslatorTrait;
    
	protected RendererResolverInterface $resolver;

    /**
     * Resolve a JSON element, using this renderers resolver.
     * 
     * @param mixed $elementJson
     * @param array $answerJson
     * @return string
     */
    protected function resolveElement(mixed $elementJson, array $answerJson) : string
    {
        if(is_array($elementJson)) {
            return $this->resolver->findRenderer($elementJson)->render($elementJson, $answerJson);
        } else {
            return $this->translateElement($elementJson);
        }
    }
    
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
    		<div class='form-description'>{$this->translateElement($jsonPart['description'])}</div>
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
    		<h{$this->resolver->getLevel()} class='{$cssClass}' style='page-break-after: avoid;'>{$this->translateElement($jsonPart['title'])}</h{$this->resolver->getLevel()}>
 HTML;
	}
}