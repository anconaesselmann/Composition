<?php
/**
 *
 */
namespace aae\serialize {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\serialize
	 */
	abstract class abstractSv implements SerializerInterface {
		protected $_delimiter = null;

		public function serialize($assoc) {
			throw new \Exception("Not Implemented yet", 1);
		}

		public function unserialize($csvString) {
			$result = array();
			$rowNames = array();
			$separator = "\r\n";
			$replacement = '\x07';

			$line = strtok($csvString, $separator);



			$i = 0;
			while ($line !== false) {
				if ($i < 1) {
					$rowNames = explode($this->_delimiter, $line);
				} else {
					if (strpos($line, '\\'.$this->_delimiter)) {
						$line = preg_replace("/\\".preg_quote($this->_delimiter)."/", $replacement, $line);
					}
					$values = explode($this->_delimiter, $line);
					$row = array();
					for ($r=0; $r < count($rowNames); $r++) { 
						$value = $values[$r];
						if ($value[0] == '"') {
							$temp = $this->_spliceNewlines($separator, $value);
							$values = array_merge($values, $temp[0]);
							$value = $temp[1];
						}
						if (strpos($value, $replacement)) {
							$value = preg_replace('/\\\\\\\\x07/', preg_quote($this->_delimiter), $line);
						}
						$row[$rowNames[$r]] = $value;
					}
					$result[] = $row;
				}
			    $line = strtok($separator);
			    $i++;
			}
			return $result;
		}

		protected function _spliceNewlines($separator, $value) {
			$additionalValues = array();
			$combinedValue = substr($value, 1);
			$matchedQuote = false;
			while (!$matchedQuote) {
				$line = strtok($separator);
				$pos = strpos($line, '"');
				if ($pos) {
					$combinedValue .= "\n".substr($line, 0, $pos);

					$lineEnding = substr($line, $pos + 1 + strlen($this->_delimiter));
					$additionalValues = explode($this->_delimiter, $lineEnding);
					$matchedQuote = true;
				} else {
					$combinedValue .= "\n".$line;
				}
				if ($line == false) {
					$matchedQuote = true;
				}
			}	
			return array($additionalValues, $combinedValue);
		}
	}
}