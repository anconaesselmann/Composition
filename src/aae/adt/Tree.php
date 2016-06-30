<?php
/**
 *
 */
namespace aae\adt {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\adt
	 */
	class Tree implements \Iterator {
		protected $_nodes = array();
		public $nodeName = "";
		protected $_parent = null;

		protected static $_s_instanceCounter = 0;

		public function __construct($nodeName = null) {
			if (is_null($nodeName)) {
				$this->nodeName = self::_getNewName();
			} else {
				$this->nodeName = $nodeName;
			}
		}
		
		protected function _getNewName() {
			return "node_".Tree::$_s_instanceCounter++;
		}
	
		public function append(&$child, $nodeName = null) {

			if (is_a($child, "aae\adt\Tree")) {
				if (!is_null($nodeName)) {
					$child->nodeName = $nodeName;
				} else {
					$nodeName = $child->nodeName;
				}
				$child->_parent = $this;
			} else {
				if (is_null($nodeName)) {
					$nodeName = $this->_getNewName();
				}
			}
			$this->_nodes[$nodeName] = $child;
		}
	
		public function nodes() {
			return $this->_nodes;
		}
	
		public function getNodeByName($nodeName) {
			return $this->_nodes[$nodeName];
		}
	
		public function getNode($nodeNbr) {
			$nodes = array_values($this->_nodes);
			return $nodes[$nodeNbr];
		}

		public function getParent() {
			return $this->_parent;
		}


		public function rewind() {
		    reset($this->_nodes);
		}

		public function current() {
		    return current($this->_nodes);
		}

		public function key() {
		    return key($this->_nodes);
		}

		public function next() {
		    next($this->_nodes);
		}

		public function valid() {
		    return key($this->_nodes) !== null;
		}
	}
}