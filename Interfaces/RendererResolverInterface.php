<?php
namespace axenox\SurveyPrinter\Interfaces;

use exface\Core\Interfaces\WorkbenchInterface;

/**
 * Use this interface for Renderer that need to resolve different format types with the right renderer class.
 * 
 * @author andrej.kabachnik, miriam.seitz
 *
 */
interface RendererResolverInterface
{
	public function findRenderer(array $jsonPart): RendererInterface;

    public function getMaxRowLength();

    public function getWorkbench() : WorkbenchInterface;
}
