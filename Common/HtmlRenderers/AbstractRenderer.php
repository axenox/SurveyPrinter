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

        // TODO: This fix is brittle and must be implemented anywhere the title attribute is read.
		// for some reason there is sometimes a `default` value and then a `de` value which actually represents what is shown as the
        // title to the user. So the logic is we take the `de` value before we take the `default`.
        // If both are empty we use the name of the jsonpart.
        // Shouldn't we also check for other languages and how do we decide which we use, maybe take the selected language from the user?
		if (is_array($jsonPart['title']) === true) {		    
		    switch (true) {
		        case $jsonPart['title']['de'] !== null && $jsonPart['title']['de'] !== '':
		            $title = $jsonPart['title']['de'];
		            break;
		        case $jsonPart['title']['default'] !== null && $jsonPart['title']['default'] !== '':
		            $title = $jsonPart['title']['default'];
		            break;
		        default:
		            $title = $jsonPart['name'];		            
		    }
		} else {
		    $title = $jsonPart['title'] ? $jsonPart['title'] : $jsonPart['name'];
		}

		return <<<HTML
    		<h{$this->resolver->getLevel()} class='{$cssClass}' style='page-break-after: avoid;'>{$title}</h{$this->resolver->getLevel()}>
 HTML;
	}
}