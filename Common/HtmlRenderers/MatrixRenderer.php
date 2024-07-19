<?php
namespace axenox\SurveyPrinter\Common\HtmlRenderers;

use axenox\SurveyPrinter\Interfaces\RendererInterface;
use exface\Core\DataTypes\DateDataType;
use exface\Core\DataTypes\DateTimeDataType;

/**
 * A strict renderer for SurveyJs panels.
 * The SurveyJs must have an array ´columns´ and ´rows´ and match one of these patterns:
 *
 * **Single choice matrix without title**
 *
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
 * **Multiple choice matrix with title**
 *
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
 * **Mixed**
 *
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
 * (!) Columns and rows of matrices without choices show columns with ´value´ and ´text´ while matrix with ´choices´ show ONLY IN COLUMNS the schema of ´name´ and ´title´ (this sould be reported to SurveyJs)
 *
 * @author miriam.seitz
 */
class MatrixRenderer extends AbstractRenderer
{	
	public function render(array $jsonPart, array $answerJson) : string
	{
		$attributes = $this->renderAttributesToRender($jsonPart);
    	$tableContent = '';

        // add empty header value for the right format of table:
        // ___|__A__|__B__|
        // _X_|__1__|__3__|
    	$tableHeader = '<th/>';
    	foreach ($jsonPart['columns'] as $column)
    	{
    		if (is_string($column)) {
    			$tableHeader .= '<th>' . $column . '</th>';
    		} else {
    			// this is ugly because SurveyJs made something foolish (see last summary entry)
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

    		$answer = $answerJson[$jsonPart['name']][$rowName];
    		foreach ($jsonPart['columns'] as $column) {
    			if (array_key_exists('choices', $jsonPart)) {
    				$columnName = is_string($column) ? $column : $column['name'];
                    $output = $this->matchChoiceWithAnswer($jsonPart['choices'], $answer, $columnName);
    				$tableContent .= '<td>' . $this->handleCellTypes($output) . '</td>';
	    		} else {
	    			$columnName = is_string($column) ? $column : $column['name'];
                    if(is_string($answer)) {
	    			    $tableContent .= '<td>' . ($answer === $columnName ? 'X' : '') . '</td>';
                    } else {
                        $tableContent .= '<td>' . $this->handleCellTypes($answer[$columnName]). '</td>';
                    }
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
     * Checks whether a string matches a certain cell type (i.e. a date or date-time) and
     * formats it accordingly.
     *
     * @param string $input
     * @return string
     */
    private function handleCellTypes(?string $input) : string
    {
        // STUB: Can be expanded with more cases, if needed.
        switch (true) {
            // NULL
            case (empty($input)):
                $input = '';
                break;
            // Date | DateTime
            case (strtotime($input)) :
                $result = DateTimeDataType::cast($input, true);
                return  DateDataType::formatDateLocalized($result, $this->resolver->getWorkbench());
        }

        return $input;
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
     * @param array $choices
     * @param mixed $answer can be either an array, a string or null
     * @return string|NULL
     */
    protected function matchChoiceWithAnswer(array $choices, mixed $answer, string $columnName) : ?string
    {
    	if (is_array($answer)){
    		if (key_exists($columnName, $answer)){
    			$answer = $answer[$columnName];
    		}
    		else {
    			return '';
    		}
    	}

    	foreach ($choices as $choice) {
    		if (is_array($choice)){
    			if ($this->matchAnswer($choice['value'], $answer)){
    				return $choice['text'];
    			}
    		} else if ($this->matchAnswer($choice, $answer)){
    			return $choice;
    		}
    	}

    	return $answer;
    }

    protected function matchAnswer(string $choice, mixed $answer) : bool
    {
    	return is_array($answer) ?
	    	$this->searchValueInMultipleAnswers($choice, $answer) :
	    	$this->matchWithAnswer($choice, $answer);
    }

    protected function searchValueInMultipleAnswers(string $choice, array $answerArray) : bool
    {
    	return in_array($choice, $answerArray);
    }

    protected function matchWithAnswer(string $choice, ?string $answer) : bool
    {
    	return $choice === $answer;
    }
    
}