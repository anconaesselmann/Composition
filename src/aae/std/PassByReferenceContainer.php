<?php
/**
 *
 */
namespace aae\std {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class PassByReferenceContainer {
		private $_value;

		public function __construct($value = null) {
			$this->_value = $value;
		}
		public function setValue($value) {
			$this->_value = $value;
		}
		public function getValue() {
			return $this->_value;
		}
	}
}