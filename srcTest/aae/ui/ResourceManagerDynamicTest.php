<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ResourceManagerDynamicTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		protected function _getTemporaryDataPath() {
			return $this->getTestDataPath()."/temp/";
		}

		protected function setUp() {
			mkdir($this->_getTemporaryDataPath());
		}

		protected function tearDown() {
			$dirPath = $this->_getTemporaryDataPath();
			foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
			    $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
			}
			rmdir($dirPath);
		}

		public function test___construct() {
			#$obj = new ResourceManagerDynamic();
		}

	}
}