<?php
namespace aae\dic {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ConfigFileContainerTest extends \PHPUnit_Framework_TestCase {
		protected function _getDataPath() {
			$reflectedClass = new \ReflectionObject($this);
			$reflectedClassFileName = $reflectedClass->getFileName();
			return dirname($reflectedClassFileName)."/".(substr(strrchr(get_class($this), "\\"), 1))."Data";
		}

		public function test___construct() {
			$path = $this->_getDataPath()."
			";
			//$json = new \aae\fs\files\JSON($path);
			$obj = new ConfigFileContainer($path);
		}
		

	}
}