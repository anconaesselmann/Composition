<?php
/**
 *
 */
namespace aae\math\general {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\math\general
	 */
	class Sequence implements \Iterator, \arrayaccess,  \countable{
		protected $_elements = array(), $_id = NULL, $_class;
		public function __construct($elements = array()) {
			$args = func_get_args();
			if (count($args) > 0) {
				if (count($args) == 1 && is_array($args[0])) {
					$this->_elements = $args[0];
				} else {
					foreach ($args as $value) {
						$this->addElement($value);
					}
				}
			}

		}
		public function getId() {
			return $this->_id;
		}
		public function setId($id) {
			$this->_id = $id;
		}
		public function __toString() {
			return implode(", ", $this->_elements);
		}
		public function addElement($point) {
			$this->_elements[] = $point;
		}

		public function rewind() {
		    reset($this->_elements);
		}

		public function current() {
		    return current($this->_elements);
		}

		public function key() {
		    return key($this->_elements);
		}

		public function next() {
		    next($this->_elements);
		}

		public function valid() {
		    return key($this->_elements) !== null;
		}

		// arrayaccess interface implementation

	    public function offsetSet($offset, $value) {
	        if (is_null($offset)) {
	            $this->_elements[] = $value;
	        } else {
	            $this->_elements[$offset] = $value;
	        }
	    }
	    public function offsetExists($offset) {
	        return isset($this->_elements[$offset]);
	    }
	    public function offsetUnset($offset) {
	        unset($this->_elements[$offset]);
	    }
	    public function offsetGet($offset) {
	        return isset($this->_elements[$offset]) ? $this->_elements[$offset] : null;
	    }


	    // Countable interface implementation
	    public function count() {
	    	return count($this->_elements);
	    }
	}
}