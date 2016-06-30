<?php
/*

 */
namespace aae\abstr\objectTraits {
	use \aae\std\std;
	/**
	 * Numbering provides functionality for uniquely numbering and naming instances
	 * and providing a rudimentary __toString implementation.
	 * Numbering of instances starts with 1.
	 *
	 * to function properly the traits constructor must be called:
	 *
	 * extend class with:
	 *
	 	use Numbering {
	    	Numbering::__construct as private _Numbering__construct;
		}
	 * then call
	 	$this->_Numbering__construct();
	 * in the  in the constructor of the class using this trait:
	 *
	 * @package aae\abstr\objectTraits
	 */
	trait Numbering {
		// accessible through __get()
		protected $INSTANCE_NUMBER;
		protected $INSTANCE_NAME;

		// inaccessible through __get()
		private static $_instanceCountArray;

		public function __construct() {
			$this->_setInstanceNumber();
			$this->_setInstanceName();
		}

		public function __toString() {
			return $this->INSTANCE_NAME;
		}

		private function _setInstanceNumber() {
			if (!isset(self::$_instanceCountArray)) {
				self::$_instanceCountArray = array();
			}
			if (!array_key_exists(get_called_class(), self::$_instanceCountArray)) {
				self::$_instanceCountArray[get_called_class()] = 1;
			} else {
				self::$_instanceCountArray[get_called_class()]++;
			}
			$this->INSTANCE_NUMBER = self::$_instanceCountArray[get_called_class()];
		}
		private function _setInstanceName() {
			$this->INSTANCE_NAME = std::classFromNSClassName(get_called_class()).$this->INSTANCE_NUMBER;
		}
	}
}