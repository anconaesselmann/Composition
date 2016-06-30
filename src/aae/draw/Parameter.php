<?php
/**
 *
 */
namespace aae\draw {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\draw
	 */
	abstract class Parameter {
        protected static $_name;
        protected $_value;
        public function __construct($value) {
            $this->_value = $value;
        }
        public function get() {
            return $this->_value;
        }
        public function getValue() {
            return $this->_value;
        }
        protected function _getStyleString() {
            return $this->getName()."=\"".$this->get()."\"";
        }
        public function __toString() {
            return $this->_getStyleString();
        }
        public function getName() {
            $className = get_class($this);
            return $className::$_name;
        }
        public function getStringValue() {
            return "\"".$this->get()."\"";
        }
    }
}