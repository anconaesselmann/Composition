<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ResourceManagerStaticTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test___construct() {
			#$obj = new ResourceManagerStatic();
		}

		/*public function test_getLinks() {
			# Given a file name as constructor argument
			$relativePath = "/a/path/to/a/file.js";
			$path = $this->getTestDataPath() . $relativePath;
			$_SERVER["DOCUMENT_ROOT"] = $this->getTestDataPath()."/";
			$obj = new ResourceManagerStatic($path);

			# When getLinks is called
			$result = $obj->getLinks();

			# Then the link relative to DOCUMENT_ROOT is returned
			$expected = $relativePath;
			$this->assertEquals($expected, $result);
		}*/

		public function provider_getHtmlLink() {
			return array(
				array($this->getTestDataPath() . "/a/path/to/a/file.js", '<script type="text/javascript" src="/a/path/to/a/file.js"></script>'),
				array($this->getTestDataPath() . "/a/path/to/a/file.css", '<link rel="stylesheet" type="text/css" href="/a/path/to/a/file.css" />')
			);
		}

		/**
		 * @dataProvider provider_getHtmlLink
		 */
		/*public function test_getHtmlLink($fileName, $expected) {
			# Given a file name as constructor argument
			$_SERVER["DOCUMENT_ROOT"] = $this->getTestDataPath()."/";
			$obj = new ResourceManagerStatic($fileName);

			# When getHtmlLink is called
			$result = $obj->getHtmlLink();

			# Then the link with the appropriate html formating is returned
			$this->assertEquals($expected, $result);
		}*/
	}
}