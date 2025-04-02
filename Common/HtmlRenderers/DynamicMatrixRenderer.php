<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

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
 * (!) The cellType does not matter since the answerJson will always have the value of the cell by name.
 * (!) The columnNames in the dynamic matrix will be equal with multiple rows but separate objects in the array.
 *
 * @author miriam.seitz
 */
class DynamicMatrixRenderer extends  AbstractRenderer
{
    private array $columnsWithSpecifiedFormat = [];

	public function render(array $jsonPart, array $answerJson) : string
	{
        $tableHeader = '';
        $rowValues = [];
		$attributes = $this->renderAttributesToRender($jsonPart);

    	foreach ($answerJson[$jsonPart['name']] as $row){
            $rowValues[] = $this->readEntireRow($jsonPart['columns'], $row);
    	}

        if (empty($rowValues)){
            return '';
        }

        foreach ($jsonPart['columns'] as $column){
            $columnName = $column['title'] ?? $column['name'];
            if (is_array($columnName)) {
                $columnName = $this->translateElement($columnName);
            }
            if (array_key_exists($columnName, $this->columnsWithSpecifiedFormat) && $this->columnsWithSpecifiedFormat[$columnName] === 'row') {
                continue;
            }

            $tableHeader .= '<th>' . $columnName  . '</th>';
        }
    	
        return <<<HTML
        
	<div style='page-break-inside: avoid;'>
		{$attributes}
		<table style='page-break-inside: avoid;'>
			<thead>
				<tr>
					{$tableHeader}
				</tr>
			</thead>
			<tbody>
				{$this->printRows($rowValues)}
			</tbody>
		</table>
	</div>
HTML;
    }

    protected function readEntireRow(array $jsonPart, array $answerJson): array
    {
        $rowValues = [];
    	foreach ($jsonPart as $column){
            $columnName = $column['title'] ?? $column['name'];
            if (is_array($columnName)) {
                $columnName = $this->translateElement($columnName);
            }
            $value = $answerJson[$column['name']];

            if (is_bool($value)) {
                $translator = $this->resolver->getTranslator();
                $value = $value === true ? $translator->translate('BOOLEAN.TRUE') : $translator->translate('BOOLEAN.FALSE');
            }

            if ($this->exceedsMaxRowLength(strlen($value))) {
                $this->columnsWithSpecifiedFormat[$columnName] = 'row';
            }

            $rowValues[$columnName] = $value;
        }

        return $rowValues;
    }

    protected function printRows(array $rows) : string
    {
        $tableContent = '';
        foreach ($rows as $rowContent => $rowValues){
            $row = '<tr>';
            $extraRow = null;
            foreach($rowValues as $column => $rowValue){
                $rowValue = $this->translateElement($rowValue);
                
                if ($this->columnsWithSpecifiedFormat[$column] === 'row') {
                    $extraRow = '<td>' . $column . '</td>'
                        . '<td style="text-align:left" colspan="'. count($rowValues)-1 .'">' . $rowValue . '</td>';
                } else {
                    $row .= '<td>' . $rowValue . '</td>';
                }
            }
            $row .= '</tr>';
            $tableContent .= $row;

            if ($extraRow !== null) {
                $tableContent .= '<tr>' . $this->translateElement($extraRow) . '</tr>';
            }
        }

        return $tableContent;
    }

    private function exceedsMaxRowLength(mixed $rowValue) : bool
    {
        return $rowValue >  $this->resolver->getMaxRowLength();
    }
}