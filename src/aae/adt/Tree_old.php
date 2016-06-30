<?php
namespace aae\adt {
	/**
	 *
	 * @package aae\adt
	 */
	class Tree {
		public $parent = NULL;
		public $data = NULL;
		public $leafs = array();

		public function __construct($data = NULL) {
			if ($data !== NULL) {
				$this->data = $data;
			}
		}
		public function __toString() {
			return (string)$this->data;
		}

		public function addLeaf($leaf) {
			$newTree = $this->_createLeaf($leaf);
			$key = (string)$newTree->data;
			if (!array_key_exists($key, $this->leafs)) {
				$this->leafs[$key] = $newTree;
				return true;
			} else {
				return false;
			}
		}
		public function editLeaf($leaf) {
			$newTree = $this->_createLeaf($leaf);
			$key = (string)$newTree->data;
			if (array_key_exists($key, $this->leafs)) {
				$this->leafs[$key] = $newTree;
				return true;
			} else {
				return false;
			}
		}
		private function _createLeaf($leaf) {
			if (gettype($leaf) == 'object' &&
				get_class($leaf) == get_called_class()) {
					$newTree = $leaf;
			} else {
				$className = get_called_class();
				$newTree = new $className();
				$newTree->data = $leaf;
			}
			$newTree->parent = $this;
			return $newTree;
		}
		public function getLeafs() {
			return $this->leafs;
		}
		public function getLeaf($stringDescriptor) {
			if (array_key_exists($stringDescriptor, $this->leafs)) {
				return $this->leafs[$stringDescriptor];
			} else {
				return false;
			}
		}
		public function getDeepLeaf(array $stringDescriptors, $depth = NULL) {
			if ($depth === NULL) {
				$depth = count($stringDescriptors);
			}
			if (count($stringDescriptors) > 0) {
				if ($depth > 0) {
					$maxIndex = $depth;
				} else {
					$maxIndex = count($stringDescriptors) + $depth;
				}
			} else {
				$maxIndex = 0;
			}
			$currentDir = $this;


			//echo "depth: $depth\n";
			for ($i=0; $i < $maxIndex; $i++) {
				$currentDir = $currentDir->getLeaf($stringDescriptors[$i]);
				if (!$currentDir) {
					return false;
				}
			}
			return $currentDir;
		}
		public function getStrHierarchy() {
			$result = array($this->data);

			$parent = $this->parent;
			while ($parent !== NULL) {
				$result[] = $parent->data;
				$parent = $parent->parent;
			}
			return array_reverse($result);
		}
		public function display() {
			$out = '';
			if (count($this->leafs) < 1) {
				$out .= implode('/', $this->getStrHierarchy())."\n";
			}

			foreach ($this->leafs as $leaf) {
				$out .= $leaf->display();
			}
			return $out;
		}
	}
}