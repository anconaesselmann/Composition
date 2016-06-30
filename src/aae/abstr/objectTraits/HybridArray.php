<?php
namespace aae\abstr\objectTraits {
	/**
	 * implements \Iterator, \ArrayAccess, \Countable
	 * These interfaces still have to be declared by the class using the trait
	 *
	 * @package aae\abstr\objectTraits
	 */
	trait HybridArray  {
		private $_dataArray = array();

		public function __construct() {

		}

		/*public function addData($data) {
			if ($data instanceof \aae\resourceManagement\Data) {
				$this->_dataArray[] = $data;
			} else {
				throw new DataException(
					constant\ERROR::NO_RESOURCE_POINTER,
					constant\ERROR::NO_RESOURCE_POINTER_ERROR_CODE
				);
			}
		}*/

		/*public function __toString() {
			$out = "";
			foreach ($this->_dataArray as $data) {
				$out .= $data->data."\n";
			}
			return $out;
		}*/

		// implementation of Iterator interface
		public function rewind() {
			reset($this->_dataArray);
		}
		public function current() {
			return current($this->_dataArray);
		}
		public function key() {
			return key($this->_dataArray);
		}
		public function next() {
			next($this->_dataArray);
		}
		public function valid() {
			return $this->key() !== null;
		}


		// implementation of ArrayAccess interface
		public function offsetExists($offset) {
			return array_key_exists($offset, $this->_dataArray);
		}

		public function offsetGet($offset) {
			if (!$this->offsetExists($offset)) {
				if (is_numeric($offset) && $offset < count($this->_dataArray)) {
					// This is extremely costly, change this to only do this once until the array changes
					$tempArray = array_values($this->_dataArray);
					return $tempArray[$offset];
				}
				throw new \Exception(constant\ERROR::INVALID_OFFSET, constant\ERROR::INVALID_OFFSET_ERROR_CODE);
			}
			return  $this->_dataArray[$offset];
		}
		private $_automaticOffset = 0;
		public function offsetSet($offset, $value) {
			 if (is_null($offset)) {
	            $this->_dataArray[$this->_automaticOffset++] = $value;
	        } else {
	            $this->_dataArray[$offset] = $value;
	        }
		}

		public function offsetUnset($offset) {
			if ($this->offsetExists($offset)) {
				unset($this->_dataArray[$offset]);
			}
		}

		// implementation of Countable interface
		public function count() {
			return count($this->_dataArray);
		}
	}
}