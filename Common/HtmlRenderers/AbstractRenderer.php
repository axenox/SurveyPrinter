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
	 * @param array $awnserJson
	 */
	public function __construct(RendererResolverInterface $resolver)
	{
		$this->resolver = $resolver;
	}
}