<?php
/**
 *
 */
namespace aae\fs {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\fs
	 */
	class File extends Path {
		public function __construct($pathString, $createNonexisting = false) {
			parent::__construct($pathString, $createNonexisting);
			#if (!is_file($this->_pathString)) throw new \Exception("Error: '$pathString' is not a file.", 213141118);
		}
	
		public function getContents() {
			return file_get_contents($this->_pathString);
		}

		protected function _create($pathString) {
			if (!file_exists(dirname($pathString))) {
				parent::_create(dirname($pathString));
			}
			$handle = fopen($pathString, "w");
			fclose($handle);
		}
	}
}