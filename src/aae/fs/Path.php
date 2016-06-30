<?php
/**
 * Path is a utility to simplify working with directories.
 */
namespace aae\fs {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\cdt
	 */
	class Path {
		protected $_pathString = null;
		public function __construct($pathString, $createNonexisting = false) {
			$pathString = $this->resolve($pathString);
			if (!file_exists($pathString)) {
				if ($createNonexisting) $this->_create($pathString);
				else throw new FileDoesNotExistException("Error: '$pathString' is not a valid path.", 213141057);
			}
			$this->_pathString = $pathString;
		}

		public function __toString() {
			return strval($this->_pathString);
		}

		public static function resolve($path) {
			if (substr($path, 0, 1) != "/") {
				$docRoot = $_SERVER["DOCUMENT_ROOT"];
				if (substr($docRoot, -1) != "/") $docRoot .= "/";
				$path    = $docRoot."../".$path;
			}
			$regex  = "/(.?)(\/[^\/]*\/\.\.)(.*)/";
			$result = preg_replace($regex, "$1$3", $path);
			if ($result != $path) $result = self::resolve($result);
			return $result;
		}

		protected function _create($pathString) {
			mkdir($pathString, 0777, true);
		}
	}
	class FileDoesNotExistException extends \Exception {}
}