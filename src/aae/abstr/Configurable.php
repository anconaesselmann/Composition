<?php
/**
 *
 */
namespace aae\abstr {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\abstr
	 */
	trait Configurable {
		public $configs = array();

		public function initConfigurable($configFileDir) {
			if (is_array($configFileDir)) {
				$this->_initializeWithArray($configFileDir);
			} else {
				$this->_initializeWithFileDir($configFileDir);
			}
		}

		private function _initializeWithFileDir($configFileDir) {
			if (!file_exists($configFileDir)) throw new \Exception("The file '$configFileDir' does not exist.", 209141613);
			
			$fileContent = file_get_contents($configFileDir);
			$fileContent = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $fileContent);

			$json = json_decode($fileContent, true);
			if (!$json) throw new \Exception("The configuration file '$configFileDir' contains invalid JSON.", 209141605);

			$this->_initializeWithArray($json);
		}

		protected abstract function _initializeWithArray($configArray);
	}
}