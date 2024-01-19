<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;

/**
 * This renderer is a strict renderer for panels of a SurveyJs.
 * The SurveyJs has to have an array ´columns´ that contains all related elements in a very specific way:
 * "type": "matrixdynamic",
 * "name": "Messwerte Förderwasser",
 * "titleLocation": "hidden",
 * "hideNumber": true,
 * "columns": [
 * 	{
 * 		"name": "elementName",
 * 		"cellType": "dropdown",
 * 		"choices": [
 * 			"item1",
 * 			"item2",
 * 			"item3",
 * 		],
 * 		"showOtherItem": true,
 * 		"otherText": "Anderer Parameter"
 * 	},
 * 	{
 * 		"name": "elementName2",
 * 		"cellType": "text",
 * 		"inputType": "number",
 * 		"min": -1
 * 	},
 * 	{
 * 		"name": "elementName3",
 * 		"cellType": "text",
 * 	}
 * ],
 *
 * The awnser json for this panel is related to the panel with an inner layer to pass for the other elements.
 * 	"dynamicMatrixName": [
 *	{
 *		"columnName1": "item2",
 *		"columnName2": 12,
 *		"columnName1": "text"
 *	},
 *	{
 *		"columnName1": "tiem1",
 *		"columnName2": 54,
 *		"columnName1": "text"
 *	},
 * ]
 * (!) The cellType does not matter since the awnserJson will always have the value of the cell by name.
 * (!) The columnNames in the dynamic matrix will be equal with mutliple rows but seperate objects in the array.
 *
 * {@inheritDoc}
 * @see \axenox\SurveyPrinter\Interfaces\RendererInterface::render()
 */
class DynamicMatrixRenderer implements RendererInterface
{	
	public function render(array $jsonPart, array $awnserJson) : string
    {        
    	foreach ($jsonPart['columns'] as $column)
    	{
    		$tableHeader .= '<th>' . $column['name'] . '</th>';
    	}
    	
    	
    	foreach ($awnserJson[$jsonPart['name']] as $row){
    		$tableContent .= '<tr>' . $this->readEntireRow($jsonPart['columns'], $row) . '</tr>';
    	}
    	
    	if ($tableContent === ''){
    		return '';
    	}
    	
        return <<<HTML
        
	<div>
		<table>
			<thead>
				<tr>
					{$tableHeader}
				</tr>
			</thead>
			<tbody>
				{$tableContent}
			</tbody>
		</table>
	</div>
HTML;
    }

    protected function readEntireRow($jsonPart, $awnserJson){
    	foreach ($jsonPart as $column){
    		$value = $awnserJson[$column['name']];
    		if (is_bool($value)){
    			$value = $value === true ? "Ja" : "Nein";
    		}
    		
    		$row .= '<td>' . $value . '</td>'; 
    	}
    	
    	return $row;
    }

    
    
}