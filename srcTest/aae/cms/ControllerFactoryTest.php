<?php
namespace aae\cms {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ControllerFactoryTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new ControllerFactory("", "");
		}

		/*public function test_build_a_leaf_instance_without_arguments() {
			# Given a json configuration object
			$namespace = "aae\\\\std\\\\";
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\"}}";
			$obj = new ControllerFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName, $namespace);
			$result = get_class($instance);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$expected = "aae\\std\\LeafStub";
			$this->assertEquals($expected, $result);
		}*/
		
	}
}