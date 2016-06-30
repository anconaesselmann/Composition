<?php
/**
 *
 */
namespace aae\draw {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\draw
	 */
	abstract class UnitParameter extends Parameter {
        protected static $_valueAppend = "px";
        public function get() {
            return $this->_value.UnitParameter::$_valueAppend;
        }
        public static function setUnit($unitString) {
            UnitParameter::$_valueAppend = $unitString;
        }
    }
}