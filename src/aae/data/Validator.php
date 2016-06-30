<?php
/**
 *
 */
namespace aae\data {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\data
	 */
	class Validator implements ValidatorInterface {
		public $validatorFactory = null;

		private $_instance = null, $_dataType, $_strict = false;

		public function __construct($dataType, $instance, $strict = true) {
			$this->validatorFactory = new \aae\data\ValidatorFactory($dataType);
			$this->_instance        = $instance;
			$this->_dataType        = (string)$dataType;
			$this->_strict          = (bool)$strict;
		}

		public function validate() {
			$dataValidator = $this->_getDataValidator();
			if ($dataValidator->validate() === true) {
				return $this->_instance;
			} else if ($this->_strict === false) {
				return $dataValidator->getDefault();
			} else {
				throw new \Exception("Error Processing Request", 211141615);
			}
		}

		public function getDefault() {}

		public function __set($varName, $value) {
			if ($varName == "strict") {
				$this->_strict = (bool)$value;
			}
			return $this;
		}

		protected function _getDataValidator() {
			return $this->validatorFactory->build();
		}
	}
}