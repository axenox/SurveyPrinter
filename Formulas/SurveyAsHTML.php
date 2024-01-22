<?php
namespace axenox\SurveyPrinter\Formulas;

use exface\Core\CommonLogic\Model\Formula;
use exface\Core\Factories\DataTypeFactory;
use exface\Core\DataTypes\HtmlDataType;
use axenox\SurveyPrinter\Common\HtmlRenderers\SurveyRenderer;
use exface\Core\DataTypes\JsonDataType;
use exface\Core\Exceptions\FormulaError;
use exface\Core\CommonLogic\Filemanager;

/**
 * Use this formular to create a HTML from a SurveyJs form within ´Formular__FormularConfig´ and the awnsers within ´FormularDaten´.
 * You can pass different design as the last parameter, the default is the ´form´ design if no parameter was passed.
 *
 * ## Different designs
 *
 * =SurveyAsHTML('{type: "text", title: "my name", name: "question1"}', '{question1: "asdf"}', 'text')
 * ```
 * <style>
 * 	   .form-text {line-height: 22px; vertical-align: middle; }
 *     .form-value {font-weight: bold;}
 * </style>
 * <div class='form-text'><label>my name</label><span class='form-value'>asdf</span></div>
 * ```
 *
 * =SurveyAsHTML('{type: "text", title: "my name", name: "question1"}', '{question1: "asdf"}', 'form')
 * ```
 * <style>
 * 	   .form-text {border: 1px solid gray; padding: 5px; height: 22px;  line-height: 22px; vertical-align: middle; }
 *     .form-text label {width: 30%; padding-right: 5px;}
 * </style>
 * <div class='form-text'><label>my name</label><span class='form-value'>asdf</span></div>
 * ```
 *
 * @author andrej.kabachnik, miriam.seitz
 *
 */
class SurveyAsHTML extends Formula
{
	const FOLDER_NAME_APPALIAS = 'axenox.SurveyPrinter';
	
	const FOLDER_NAME_DESGINS = 'Designs';
	
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\CommonLogic\Model\Formula::run()
     */
	public function run(string $surveyJson = null, string $awnserJson = null, string $design = 'form_dashed') : string
    {
    	$renderersByType = $this->getWorkbench()->getApp(self::FOLDER_NAME_APPALIAS)
    		->getConfig()->getOption('RENDERERS_BY_TYPE')->toArray();
    	
    	if ($awnserJson === null){
    		return 'The requested Formular has not been filled out yet.';
    	}
    	
    	try {
    		$surveyJson = JsonDataType::decodeJson($surveyJson);
    		$awnserJson = JsonDataType::decodeJson($awnserJson);
    	} catch(\Throwable $e) {
    		$this->getWorkbench()->getLogger()->logException(
    			new FormulaError('Given JSON cannot be resolved. Please validate format.', null, $e));
    		return 'Given JSON cannot be resolved. Please validate format.';
    	}
    	
    	return (new SurveyRenderer($this->getWorkbench(), $renderersByType))->render($surveyJson, $awnserJson, $this->getStyleCssPath($design));
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\Core\CommonLogic\Model\Formula::getDataType()
     */
    public function getDataType()
    {
        return DataTypeFactory::createFromPrototype($this->getWorkbench(), HtmlDataType::class);
    }
    
    protected function getStyleCssPath($design)
    {
    	$directories = [
    		$this->getWorkbench()->getApp(self::FOLDER_NAME_APPALIAS)->getDirectoryAbsolutePath(),
    		self::FOLDER_NAME_DESGINS,
    		$design . '.css'
    	];
    	
    	return implode(DIRECTORY_SEPARATOR, $directories);
    }
}