<?php
/**
 *
 */
namespace aae\dispatch\receiver {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\dispatch\receiver
	 */
	abstract class ReiceiverAbstract implements ReceiverInterface {
		protected $_container = array();

		/* ReceiverInterface implementation */
		public function get($varname = false) {
			if ($varname) {
				return $this->_container[$varName];
			} else {
				return $this->_container;
			}
		}
		/* arrayaccess interface implementation */
		public function offsetSet($offset, $value) {
			if (is_null($offset)) {
				$this->_container[] = $value;
			} else {
				$this->_container[$offset] = $value;
			}
		}
		public function offsetExists($offset) {
			return isset($this->_container[$offset]);
		}
		public function offsetUnset($offset) {
			unset($this->_container[$offset]);
		}
		public function offsetGet($offset) {
			return isset($this->_container[$offset]) ? $this->_container[$offset] : null;
		}
		public function getRequestArray() {
			return $this->_container;
		}
	}
}