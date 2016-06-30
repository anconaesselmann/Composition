<?php
/**
 *
 */
namespace aae\geo {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo
	 */
	class Track implements \arrayaccess, \Iterator, \Countable{
		public $name = "";

		private $position = 0;
		private $container = array();
		public $segmentStarts = array();

		public function __construct() {
	        $this->position = 0;
	        $this->segmentStarts[] = 0;
	    }

	    public function __toString() {
	    	$result = "";
	    	foreach ($this->container as $point) {
	    		$result .= $point->__toString()."\n";
	    	}
	    	return $result;
	    }


		// arrayaccess interface implementation

	    public function offsetSet($offset, $value) {
	        if (is_null($offset)) {
	            $this->container[] = $value;
	        } else {
	            $this->container[$offset] = $value;
	        }
	    }
	    public function offsetExists($offset) {
	        return isset($this->container[$offset]);
	    }
	    public function offsetUnset($offset) {
	        unset($this->container[$offset]);
	    }
	    public function offsetGet($offset) {
	        return isset($this->container[$offset]) ? $this->container[$offset] : null;
	    }

	    // Iterator interface implementation
	    function rewind() {
	        $this->position = 0;
	    }

	    function current() {
	        return $this->container[$this->position];
	    }

	    function key() {
	        return $this->position;
	    }

	    function next() {
	        ++$this->position;
	    }

	    function valid() {
	        return isset($this->container[$this->position]);
	    }

	    // Countable interface implementation
	    public function count() {
	    	return count($this->container);
	    }
	}
}