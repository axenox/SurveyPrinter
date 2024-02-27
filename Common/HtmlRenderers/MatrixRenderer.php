<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * This renderer is a strict renderer for panels of a SurveyJs.
 * The SurveyJs has to have an array ´columns´ and ´rows´ that differ regarding the presents of choices and text or title:
 * one choice matrix without title
 * "type": "matrix",
 * "name": "question1",
 * "title": "Matrix single choice", // optional
 * "description": "with values", // optional
 * "hideNumber": true,
 * "columns": [
 * 		"Column 1",
 * 		"Column 2",
 * 		"Column 3"
 * ],
 * "rows": [
 * 		"Row 1",
 * 		"Row 2"
 * ]
 * 
 * multiple choice matric with title
 * ´"type": "matrixdropdown",
 * "name": "question3",
 * "title": "Matrix multiple choice", // optional
 * "description": "with title", // optional
 * "columns": [
 * 	{
 * 		"name": "Column 1",
 * 		"title": "Spalte 1"
 * 	},
 * 	{
 * 		"name": "Column 2",
 * 		"title": "Spalte 2"
 * 	},
 * ],
 * "choices": [
 * 	{
 * 		"value": 1,
 * 		"text": "Choice1"
 * 	},
 * 	{
 * 		"value": 2,
 * 		"text": "Choice2"
 * 	}
 * ],
 * "rows": [
 * 	{
 * 		"value": "Row 1",
 * 		"text": "Title1"
 * 	},
 * 	{
 * 		"value": "Row 2",
 * 		"text": "Title2"
 * 	}
 * ]´
 * 
 * The awnser differs as well regarding the presents of choices: * 
 * 	´"matrixSingleChoiceName": {
 *		"Row 1": "Column 1",
 *		"Row 2": "Column 2"
 *	},
 *	"matrixMultipleChoiceName": {
 *		"Row 1": {
 *			"Column 1": 2,
 *			"Column 2": 1,
 *			"Column 3": 1
 *		},
 *		"Row 2": {
 *			"Column 2": 2,
 *			"Column 3": 1
 *		}
 *	},
 * ]´
 * (!) columns and rows of matrix without choices shows columns with ´value´ and ´text´ while matrix with ´choices´ show ONLY IN COLUMNS the schema of ´name´ and ´title´ (this sould be reported to SurveyJs)
 *
 * @author miriam.seitz
 */
class MatrixRenderer extends AbstractRenderer
{	
	public function render(array $jsonPart, array $awnserJson) : string
	{
		$attributes = $this->renderAttributesToRender($jsonPart);
    	$tableContent = '';
    	
    	// add empty row name column
    	$tableHeader .= '<th/>';
    	foreach ($jsonPart['columns'] as $column)
    	{    		
    		if (is_string($column)) {
    			$tableHeader .= '<th>' . $column . '</th>';
    		} else {    			
    			// this is ugly becaus SurveyJs made something stupid (see last summary entry)
	    		$tableHeader .= '<th>' . ($column['text'] ?? $column['value'] ?? $column['title'] ?? $column['name']) . '</th>';
    		}
    	}
    	
    	foreach ($jsonPart['rows'] as $row){
    		$tableContent .= '<tr>';
    		
    		$rowEntryTitleCssClass = 'form-row-title-entry';
    		if (is_string($row)) {
    			$rowName = $row;
    			$tableContent .= '<td class=' . $rowEntryTitleCssClass .'>' . $row . '</td>';
    		} else {
    			$rowName = $row['value'];
    			$tableContent .= '<td class=' . $rowEntryTitleCssClass .'>'. ($row['text'] ?? $row['value']) . '</td>';
    		}
    		
    		$awnser = $awnserJson[$jsonPart['name']][$rowName];
    		foreach ($jsonPart['columns'] as $column){
    			if (array_key_exists('choices', $jsonPart)) {
    				$columnName = is_string($column) ? $column : $column['name'];
    				$tableContent .= '<td>' . $this->matchChoiceWithAwnser($jsonPart['choices'], $awnser, $columnName) . '</td>';
	    		} else {
	    			$columnName = is_string($column) ? $column : $column['value'];
	    			$tableContent .= '<td>' . ($awnser === $columnName ? 'X' : '') . '</td>';
	    		}
    		}
    		
    		$tableContent .= '</tr>';
    	}    	
    	
    	if ($tableContent === ''){
    		return '';
    	}
    	
        return <<<HTML
        
	<div style='page-break-inside: avoid;'>
		{$attributes}
		<table style='page-break-inside: avoid;'>
			<thead>
				{$tableHeader}
			</thead>
			<tbody>
				{$tableContent}
			</tbody>
		</table>
	</div>
HTML;
    }
    
    /**
     * Evaluates the choice either 
     * with text
     *  {
     * 		"value": "item1",
     * 		"text": "A"
     *  }
     * or without:
     * value: "item1"
     *
     * @param array $choice 
     * @param mixed $awnser can be either an array, a string or null
     * @return string|NULL
     */
    protected function matchChoiceWithAwnser(array $choices, mixed $awnser, string $columnName) : ?string
    {
    	if (is_array($awnser)){
    		if (key_exists($columnName, $awnser)){
    			$awnser = $awnser[$columnName];    			
    		}
    		else {
    			return '';
    		}
    	}
    	
    	foreach ($choices as $choice) {
    		if (is_array($choice)){
    			if ($this->matchAwnser($choice['value'], $awnser)){
    				return $choice['text'];
    			}
    		} else if ($this->matchAwnser($choice, $awnser)){
    			return $choice;
    		}
    	}
    	
    	return '';
    }
    
    protected function matchAwnser(string $choice, mixed $awnser) : bool
    {
    	return is_array($awnser) ?
	    	$this->searchValueInMultipleAwnsers($choice, $awnser) :
	    	$this->matchWithAwnser($choice, $awnser);
    }
    
    protected function searchValueInMultipleAwnsers(string $choice, array $awnserArray) : bool
    {
    	return in_array($choice, $awnserArray);
    }
    
    protected function matchWithAwnser(string $choice, ?string $awnser) : bool
    {
    	return $choice === $awnser;
    }
    
}