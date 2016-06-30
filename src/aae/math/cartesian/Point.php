<?php
/**
 *
 */
namespace aae\math\cartesian {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\math\cartesian
	 */
	class Point implements \Iterator {
		protected $_values = array("x" => null, "y" => null, "z" => null, "time" => null);

		public function __construct($x, $y, $z = null) {
			$this->_values["x"] = $x;
			$this->_values["y"] = $y;
			$this->_values["z"] = $z;
		}

		public function __set($property, $value) {
			if (array_key_exists($property, $this->_values)) {
				$this->_values[$property] = $value;
			}
			return $this;
		}

		public function __get($property) {
			if (array_key_exists($property, $this->_values)) {
				return $this->_values[$property];
			} else {
				return null;
			}
		}

		public function __toString() {
			$result = "(" . $this->_values["x"]. ", " . $this->_values["y"];
			if (!is_null($this->_values["z"] )) {
				$result .= ", " . $this->_values["z"];
			}
			$result .= ")";
			if (!is_null($this->_values["time"])) {
				$result .= ", time = ". $this->_values["time"];
			}
			return $result;
		}

		function rewind() {
		    reset($this->_values);
		}

		function current() {
		    return current($this->_values);
		}

		function key() {
		    return key($this->_values);
		}

		function next() {
		    next($this->_values);
		}

		function valid() {
		    return !is_null(key($this->_values)) && !is_null($this->current());
		}
	}
}