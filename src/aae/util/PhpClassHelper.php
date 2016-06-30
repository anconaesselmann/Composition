<?php
/**
 *
 */
namespace aae\util {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\util
	 */
	class PhpClassHelper {
        protected $_fileName;

        public function setFileName($fileName) {
            $this->_fileName = $fileName;
        }


        private function _getFileContent() {
            return file_get_contents($this->_fileName);
        }
        public function getFunctions($functionEnding = "") {
            $fileContent = $this->_getFileContent();
            $matches     = [];
            preg_match_all('/(function[\s]*)([a-zA-Z0-9_\-]*'.$functionEnding.')(\s*\()/s', $fileContent, $matches);
            $functions   = $matches[2];
            return $functions;
        }
    }
}