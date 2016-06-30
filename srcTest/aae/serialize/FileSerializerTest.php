<?php
namespace aae\serialize {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class FileSerializerTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;
		/**
		 * TEST_DESCRIPTION
		 */
		public function test_unserialize() {
			// Setup
			$serializer = new \aae\serialize\Json();
			$obj = new FileSerializer($serializer);
			$expected = array("test1" => "one", "test2" => "two");

			$fileDir = $this->getTestDataPath()."/test1.json";

			// Testing
			$result = $obj->unserialize($fileDir);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_serialize() {
			// Setup
			$serializer = new \aae\serialize\Json();
			$obj = new FileSerializer($serializer);
			$instance = array("test1" => "one", "test2" => "two");

			$fileDir = $this->getTestDataPath()."/temp.json";

			// Testing
			$obj->serialize($instance, $fileDir);
			// 
			$result = $obj->unserialize($fileDir);
			
			// Verification
			$this->assertEquals($instance, $result);
		}
	}
}