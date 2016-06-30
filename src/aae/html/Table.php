<?php 
namespace aae\html {
	/**
	 * An HTML table.
	 *		- constructor accepts 0 for number of rows, but for now the columns NEED to be the final number
	 *		- rows can be inserted at any position with insertRow(). The row initially at that position and all rows after it will move one row down
	 *		- rows can be added to the end of the table with addRow()
	 *		- deleteRow() removes the row at the given index and moves all following rows one down. Exponential big O !!!!! Maybe use linked lists?
	 *		- setNbrRows() inserts blank rows or removes rows, according to the table size before the function is called
	 *		- getNbrRows() and getNbrColumns() behave like expected
	 *		- the member variable $id is public and can be set to give the table an id for CSS application
	 *		- the column_widht_array needs work. Here a width can be passed to set the column width of the table
	 *		
	 * to do: 
	 *	- let the user remove and add columns
	 *	- work on col_width_array
	 */
	class Table extends \aae\html\HTMLObject {
		public function __construct($rows = 0, $columns = 0) {
			$this->n = (int)$columns;
			if ( ($columns > 0) &&
				 ($rows > 0) )
			{
				// if the user specified spreadsheet dimensions, populate $this->row_array with specified ammount of empty rows
				for ($i = 0; $i < $rows; $i++) {
					$this->row_array[] = $this->createBlankRow();
				}
			}
		}
		public function toHtml() {
			$indent = $this->indent;
			if (!empty($this->id)) {
				$id_string = ' id"'.$this->id.'"';
			}
			$output	= 	 		"\n"
								.row('<table'.$id_string.'>', $indent);
			$indent++;
			for ($i = 0; $i < count($this->row_array); $i++) {
				$output .=	 	 row(	'<tr>', $indent);
				for ($n = 0; $n < $this->n; $n++) {
					$data_string = $this->row_array[$i][0][$n];				// cheating for now
					if ($data_string === NULL) {
						$data_string = '-'; 
					}
					$output .=	 row(		'<td>', ++$indent)
								.row(			$data_string, ++$indent)
								.row(		'</td>', --$indent);
					$indent--;
				}
				$output .=	 	 row(	'</tr>', $indent);
			}
			$output .=	 		 row('</table>', --$indent);
			return $output;
		}
		public function __toString() {
			return $this->toHtml();
		}
		// inserts a row at the index given.
		// if index larger than numbers of rows, the row will be inserted as the new last row
		// if the row to be inserted has more columns than the spreadsheet, extra columns will not be inserted
		// if the row has fewer columns, the additional columns will be filled with NULL
		// returns true if successfull, false if not successful
		public function insertRow($column_array, $m) {
			if (!empty($column_array)) {
				$column_count_new_row 	= count($column_array);
				$column_count_ss		= (int)$this->n;
				if ($column_count_new_row <= $column_count_ss) {
					// fill $columns with new data
					for ($i = 0; $i < $column_count_new_row; $i++) {
						if ($column_array[$i] === '') {
							$columns[] = $this->createBlankColumnVal();
						} else $columns[] = $column_array[$i];
					}
					// padd $columns with NULL cells if necessary
					$nbr_empty_columns = $column_count_ss - $column_count_new_row;
					for ($i = 0; $i < $nbr_empty_columns; $i++) {
						$columns[] = $this->createBlankColumnVal();
					}
				} else {
					// only fill $columns with as many values as the spreadsheet can hold
					for ($i = 0; $i < $column_count_ss; $i++) {
						if ($column_array[$i] === '') {
							$columns[] = $this->createBlankColumnVal();
						} else $columns[] = $column_array[$i];
					}
				}
				$last_row_index = count($this->row_array) - 1;
				if ($last_row_index < 0) {
					$last_row_index = 0;
				}
				if ($m > $last_row_index + 1) {
					$insertRow_index = $last_row_index + 1;
				} else {
					$insertRow_index = $m;
				}
				if ($last_row_index > 0) {
					for ($i = $last_row_index + 1; $i > $insertRow_index; $i--) {
						$this->row_array[$i] = $this->row_array[$i - 1];				
					}
				}
				$this->row_array[$insertRow_index] = $columns;
				return true;
			} else return false;
			
		}
		// adds a row to the end of the spreadsheet
		public function addRow($column_array) {
			$m = count($this->row_array);
			return $this->insertRow($column_array, $m);
		}
		public function deleteRow($row_number) {
			// move all rows down one starting with the row above $row_number
			for ($i = $row_number; $i < count($this->row_array); $i++) {
				$this->row_array[$i] = $this->row_array[$i + 1];
			}
			// delete last row
			$index_last_row = count($this->row_array) - 1;
			unset($this->row_array[$index_last_row]);
		}
		// 	if $nbr_rows is smaller then the current ammount of rows, this function deletes all rows larger than $nbr_rows. 
		//	If $nbr_rows is larger than the ammount of current rows, this function will create empty rows and append them to the spreadsheet
		public function setNbrRows($nbr_rows) {
			$row_count = count($this->row_array);
			if ($nbr_rows < $row_count) {
				// delete eccess rows
				for ($i = $row_count - 1; $i >= $nbr_rows - 1; $i--  ) {
					$this->deleteRow($i);
				}
			} else {
				// create blank rows
				for ($i = 0; $i < $nbr_rows - $row_count; $i++) {
					$this->row_array[] = $this->createBlankRow();
				}
			}
		}
		protected function createBlankColumnVal() {
			return NULL;
		}
		protected function createBlankRow() {
			for ($i = 0; $i < $this->n; $i++) {
				$columns[$i] = $this->createBlankColumnVal();
			}
			return $columns;
		}
		public function getNbrRows() {
			return count($this->row_array);
		}
		public function getNbrColumns() {
			return $this->n;
		}
		protected $row_array;
		protected $n;	// number of columns
		public $indent;
		public $id;
		public $col_width_array;
	}
}