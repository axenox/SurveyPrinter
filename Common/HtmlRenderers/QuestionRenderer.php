<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * The QuestionRenderer is an abstract class ensuring the context of every renderer that deals with awnsers.
 *
 * @author miriam.seitz
 *
 */
abstract class QuestionRenderer implements RendererInterface
{	    
    protected function isPartOfAwnserJson(array $jsonPart, array $awnserJson) : bool
    {
    	if ($awnserJson[$jsonPart['name']] !== null) {
    		return true;
    	}
    	
    	return false;
    }
}