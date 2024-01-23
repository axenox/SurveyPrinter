<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Formulas\SurveyAsHTML;
use axenox\SurveyPrinter\Interfaces\RendererInterface;
use axenox\SurveyPrinter\Interfaces\RendererResolverInterface;
use exface\Core\Exceptions\InvalidArgumentException;
use exface\Core\Exceptions\Configuration\ConfigOptionNotFoundError;
use exface\Core\CommonLogic\Workbench;

/**
 * The SurveyRenderer takes a SurveyJs and tries to resolve all it's elements. He needs an array of all renderers by type
 * to assign the renderers to the right format type.
 * 
 * @author andrej.kabachnik, miriam.seitz
 */
class SurveyRenderer implements RendererInterface,  RendererResolverInterface
{
	protected Workbench $workbench;
	protected array $renderersByType;
	private  int $level = 1;
	
	public function __construct(Workbench $workbench, array $renderersByType)
	{		
		$this->workbench = $workbench;
		$this->renderersByType = $renderersByType;
	}
	
	/**
	 * Renders every element of the gives survey JSON into an HTML.
	 *
	 * @param array $jsonPart
	 * @return string
	 */
    public function render(array $surveyJson, array $awnserJson, string $cssPath = null): string
    {
    	return $this->createStyleHeader($cssPath) . $this->renderElements($surveyJson, $awnserJson);
    }
    
    /**
     *
     * @param array $json
     * @return string
     */
    public function renderElements(array $json, $awnserJson) : string
    {
        return $this->findRenderer($json)->render($json, $awnserJson);
    }
        
    public function findRenderer(array $jsonPart): RendererInterface
    {
    	
    	if (array_key_exists('type', $jsonPart) === false){
    		return new $this->renderersByType['none']($this);
    	}
    	
    	if (array_key_exists($jsonPart['type'], $this->renderersByType) === false){
    		$this->workbench->getLogger()->logException(new ConfigOptionNotFoundError(
    			$this->workbench
	    			->getApp(SurveyAsHTML::FOLDER_NAME_APPALIAS)
	    			->getConfig(),
    			'Unkown render target type: ' . $jsonPart['type']));
    		return new InvisibleRenderer($this); // TODO: delete when not found handler implemented
    		// return new NotFoundRenderer($this); 
    	}
    	
    	return new $this->renderersByType[$jsonPart['type']]($this);
    }
    
    protected function createStyleHeader(string $cssPath) : string 
    {
    	$css = file_get_contents($cssPath);
    	if ($css === false){
    		throw new InvalidArgumentException('No css with the name \'' . $cssPath . '\' found!');
    	}
    	
    	return <<<HTML

	<style>
		{$css}
	</style>
HTML;
    }
    
    public function getLevel() : int 
    {
    	return $this->level;
    }
    
    public function increaseLevel() : void
    {
    	if ($this->level === 6){
    		return;
    	}
    	
    	$this->level++;
    }
    
    public function decreaseLevel() : void
    {
    	if ($this->level === 0){
    		$this->level;
    	}
    	
    	$this->level--;
    }

}