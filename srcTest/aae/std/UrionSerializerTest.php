<?php
namespace aae\std {
	require_once strstr(__FILE__, "Test", true)."/aae/autoload/AutoLoader.php";
	class UrionSerializerTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new UrionSerializer();
		}
		public function test_serialize_with_indexed_array() {
			// Setup
			$obj = new UrionSerializer();
			$expected = 'one,two,3';

			$instance = array('one', 'two', '3');
		
			// Testing
			$result = $obj->serialize($instance);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_unserialize_with_indexed_array() {
			// Setup
			$obj = new UrionSerializer();
			$instance = 'one,two,3';

			$expected = array('one', 'two', '3');
		
			// Testing
			$result = $obj->unserialize($instance);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_serialize_with_array() {
			// Setup
			$obj = new UrionSerializer();
			$expected = "(test1:one,test2:two)";

			$instance = array('test1' => 'one', 'test2' => 'two');
		
			// Testing
			$result = $obj->serialize($instance);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_serialize_with_object_implementing_JsonSerializable() {
			// Setup
			$obj = new UrionSerializer();
			$expected = "(test1:one,test2:two)";

			$instance = new FuJson_10_21_2014();
		
			// Testing
			$result = $obj->serialize($instance);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function test_serialize_exception_object_doesnt_implement_JsonSerializable() {
			// Setup
			$obj = new UrionSerializer();
			$expected = "(test1:one,test2:two)";

			$instance = new \stdClass();
		
			// Testing
			try {
				$result = $obj->serialize($instance);
			} catch (\Exception $e) {
				$this->assertEquals(216141337, $e->getCode());
				return;
			}
			$this->fail('An exception should have been thrown, since the object passed to serialize did not implement the JsonSerializable interface.');
		}

		public function test_unserialize() {
			// Setup
			$obj = new UrionSerializer();
			$expected = array('test1' => 'one', 'test2' => 'two');
			$string = "(test1:one,test2:two)";
		
			// Testing
			$result = $obj->unserialize($string);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		/*public function test_unserialize_with_comment() {
			// Setup
			$obj = new UrionSerializer();
			$expected = array('test1' => 'one', 'test2' => 'two');
			$string = "{'test1':'one', /* This is a comment *//*\n'test2':'two')";
		
			// Testing
			$result = $obj->unserialize($string);
			
			// Verification
			$this->assertEquals($expected, $result);
		}*/

		public function test_unserialize_exception_invalid_json() {
			// Setup
			$obj = new UrionSerializer();
			$expected = array('test1' => 'one', 'test2' => 'two');
			$string = "('test1':'one','test2':'two'";
		
			// Testing
			try {
				$result = $obj->unserialize($string);
			} catch (\Exception $e) {
				$this->assertEquals(216141338, $e->getCode());
				return;
			}
			$this->fail('An exception should have been thrown, since string passed to serialize contained invalid json.');
		}
	}

	class FuJson_10_21_2014 implements \JsonSerializable {
		public function jsonSerialize() {
			return array('test1' => 'one', 'test2' => 'two');
		}
	}
		
}