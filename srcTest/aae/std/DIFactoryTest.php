<?php
namespace aae\std {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DIFactoryTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new DIFactory("");
		}

		public function test_build_retrieve_a_configuraion_value() {
			# Given a json configuration object
			$instanceName = "value";
			$value = "testString";
			$json = "{\"$instanceName\": \"$value\"}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$result = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$expected = $value;
			$this->assertEquals($expected, $result);
		}

		public function test_build_a_leaf_instance_without_arguments() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\"}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			$result = get_class($instance);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$expected = "aae\\std\\LeafStub";
			$this->assertEquals($expected, $result);
		}

		public function test_build_a_leaf_instance_with_alternate_naming() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae/std/LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\"}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			$result = get_class($instance);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$expected = "aae\\std\\LeafStub";
			$this->assertEquals($expected, $result);
		}

		public function test_build_a_leaf_instance_with_explicit_arguments() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\",\"args\": [1, 2]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			$result1 = $instance->val1;
			$result2 = $instance->val2;
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(1, $result1);
			$this->assertEquals(2, $result2);
		}

		public function test_build_a_leaf_instance_with_configuration_value() {
			# Given a json configuration object
			$confValName = "value";
			$confVal = "testString";
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$confValName\": \"$confVal\",\"$instanceName\": {\"class\": \"$value\",\"args\": [{\"dep\": \"$confValName\"}]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			$result = $instance->val1;
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals($confVal, $result);
		}

		public function test_build_a_leaf_instance_with_mixed_values() {
			# Given a json configuration object
			$confValName = "value";
			$confVal = "testString";
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$confValName\": \"$confVal\",\"$instanceName\": {\"class\": \"$value\",\"args\": [{\"dep\": \"$confValName\"},5]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			$result = $instance->val1;
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals($confVal, $result);
			$this->assertEquals($instance->val2, 5);
		}

		public function test_build_a_non_singleton() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\"}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance1 = $obj->build($instanceName);
			$instance1->val1 = 1;
			$instance2 = $obj->build($instanceName);
			$instance2->val1 = 2;
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(1, $instance1->val1);
		}

		public function test_build_a_singleton() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\",\"static\":true}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance1 = $obj->build($instanceName);
			$instance1->val1 = 1;
			$instance2 = $obj->build($instanceName);
			$instance2->val1 = 2;
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(2, $instance1->val1);
		}

		public function test_build_call_setter_with_no_arg() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\",\"setters\":[\"setWasCalled\"]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(true, $instance->wasCalled);
		}

		public function test_build_call_setter_with_one_primitive_arg() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\",\"setters\":[{\"setValue\":5}]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(5, $instance->val1);
		}
		public function test_build_call_setter_with_multiple_primitive_args() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$instanceName\": {\"class\": \"$value\",\"setters\":[{\"setValue\":[5,10]}]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(5, $instance->val1);
			$this->assertEquals(10, $instance->val2);
		}
		public function test_build_call_setter_with_dependency() {
			# Given a json configuration object
			$depName = "aDependency";
			$depValue = 5;
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$depName\": \"$depValue\",\"$instanceName\": {\"class\": \"$value\",\"setters\":[{\"setValue\":{\"dep\":\"$depName\"}}]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(5, $instance->val1);
		}
		public function test_build_call_setter_with_dependency_and_other_args() {
			# Given a json configuration object
			$depName = "aDependency";
			$depValue = 5;
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$depName\": \"$depValue\",\"$instanceName\": {\"class\": \"$value\",\"setters\":[{\"setValue\":[{\"dep\":\"$depName\"},10]}]}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(5, $instance->val1);
			$this->assertEquals(10, $instance->val2);
		}


		public function test_build_get_evaluated_without_args() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{
    \"$instanceName\": {
        \"class\": \"$value\",
        \"args\": [
            5
        ],
        \"evaluate\": \"getValue\"
    }
}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$result = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(5, $result);
		}

		public function test_build_get_evaluated_primitive_args() {
			# Given a json configuration object
			$instanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{
    \"$instanceName\": {
        \"class\": \"$value\",
        \"args\": [
            5
        ],
        \"evaluate\": {
            \"getValueSum\": [
                5,
                10
            ]
        }
    }
}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$result = $obj->build($instanceName);
			
			# Then an instance with all dependencies
			# declared in the configuration object is created
			$this->assertEquals(15, $result);
		}

		public function test_build_IncompleteInstanceObject_without_args_and_resolve_parent_constructor_args() {
			# Given a json configuration object with a nonexistent class name
			$instanceName = "aae/std/ExtendsLeafStub";
			$parentInstanceName = "leafStub";
			$value = "aae\\\\std\\\\LeafStub";
			$json = "{\"$parentInstanceName\": {\"class\": \"$value\"}}";
			$obj = new DIFactory(json_decode($json, true));
		
			# When build is called
			$instance = $obj->extend($parentInstanceName)->build($instanceName);
			$result = get_class($instance);
			
			# Then an IncompleteInstanceObject is returned with the name of the
			# instance to be build.
			$expected1 = "aae\\std\\ExtendsLeafStub";
			$this->assertEquals($expected1, $result);
		}


		
	}
	class LeafStub {
		public $val1, $val2, $wasCalled = false;
		public function __construct($val1 = null, $val2 = null) {
			$this->val1 = $val1;
			$this->val2 = $val2;
		}
		public function setValue($val1, $val2 = null) {
			$this->val1 = $val1;
			$this->val2 = $val2;
		}
		public function setWasCalled() {
			$this->wasCalled = true;
		}
		public function getValue($valNbr = 1) {
			return $this->{"val".$valNbr};
		}
		public function getValueSum($val1, $val2) {
			return $val1 + $val2;
		}
	}
	class ExtendsLeafStub extends LeafStub {

	}

}