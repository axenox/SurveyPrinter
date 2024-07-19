<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Formulas\SurveyAsHTML;
use axenox\SurveyPrinter\Interfaces\RendererInterface;
use axenox\SurveyPrinter\Interfaces\RendererResolverInterface;
use exface\Core\Exceptions\InvalidArgumentException;
use exface\Core\Exceptions\Configuration\ConfigOptionNotFoundError;
use exface\Core\CommonLogic\Workbench;
use exface\Core\Interfaces\ConfigurationInterface;
use exface\Core\Interfaces\WorkbenchInterface;

/**
 * The SurveyRenderer takes a SurveyJs and tries to resolve all it's elements. He needs an array of all renderers by type
 * to assign the renderers to the right format type.
 * 
 * @author andrej.kabachnik, miriam.seitz
 */
class SurveyRenderer implements RendererInterface,  RendererResolverInterface
{
    private $config;

	protected Workbench $workbench;
	protected array $renderersByType;
	private  int $headingLevel;
    private int $maxRowLength;
	
	public function __construct(Workbench $workbench, ConfigurationInterface $config)
	{
        $this->config = $config;
		$this->workbench = $workbench;
		$this->renderersByType = $config->getOption('RENDERERS_BY_TYPE')->toArray();
        $this->maxRowLength =  $config->getOption('TABLE_CONFIG')->toArray()['MAX_ROW_LENGTH'];
	}

    /**
     *
     * @param array $surveyJson
     * @param array $answerJson
     * @param string|null $cssPath
     * @param int $headingLevel
     * @return string
     */
    public function render(array $surveyJson, array $answerJson, string $cssPath = null, int $headingLevel = 1): string
    {
    	$this->headingLevel = $headingLevel;
    	return $this->createStyleHeader($cssPath) . $this->renderElements($surveyJson, $answerJson);
    }
    
    /**
     *
     * @param array $json
     * @return string
     */
    public function renderElements(array $json, $answerJson) : string
    {
        return $this->findRenderer($json)->render($json, $answerJson);
    }
        
    public function findRenderer(array $jsonPart): RendererInterface
    {
    	if (array_key_exists('type', $jsonPart) === false){
    		return new $this->renderersByType['none']($this);
    	}
    	
    	if (array_key_exists($jsonPart['type'], $this->renderersByType) === false){
    		$this->workbench->getLogger()->logException(new ConfigOptionNotFoundError(
    			$this->config,
    			'Unknown render target type: ' . $jsonPart['type']));
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
    	return $this->headingLevel;
    }
    
    public function increaseLevel() : void
    {
    	if ($this->headingLevel === 6){
    		return;
    	}
    	
    	$this->headingLevel++;
    }
    
    public function decreaseLevel() : void
    {
    	if ($this->headingLevel === 0){
    		return;
    	}
    	
    	$this->headingLevel--;
    }

    public function getMaxRowLength()
    {
        return $this->maxRowLength;
    }

    public function getWorkbench() : WorkbenchInterface
    {
        return $this->workbench;
    }

    public function getTranslator(): \exface\Core\Interfaces\TranslationInterface
    {
        return $this->workbench->getApp('axenox.SurveyPrinter')->getTranslator();
    }
}