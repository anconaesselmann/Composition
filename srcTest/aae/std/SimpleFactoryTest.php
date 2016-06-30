<?php
namespace aae\std {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class SimpleFactoryTest extends \PHPUnit_Framework_TestCase {
		public function test_build_without_constructor_args() {
			$className = 'aae\\std\\SampleObject';
			$obj = new SimpleFactory();
			$construcedObject = $obj->build($className);
			$result = $construcedObject->fu();
			$this->assertEquals(false, $result);
		}

		public function test_build_with_constructor_args() {
			$className = 'aae\\std\\SampleObject';
			$obj = new SimpleFactory();
			$construcedObject = $obj->build($className, true, true, true);
			$result = $construcedObject->fu();
			$this->assertEquals(true, $result);
		}

		public function test_build_exception_nonexistent_class() {
			$className = 'aae\\std\\NonexistentClass';
			$obj = new SimpleFactory();
			try {
				$construcedObject = $obj->build($className);
			} catch (\Exception $e) {
				$this->assertEquals(211141513, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since the specified class name does not exist");
		}
		
		public function test_buildWithArgsArray() {
			// Setup
			$className = 'aae\\std\\SampleObject';
			$constructorArgs = array(true, true, true);
			$obj = new SimpleFactory();
		
			// Testing
			$construcedObject = $obj->buildWithArgsArray($className, $constructorArgs);
			$result = $construcedObject->fu();
			// Verification
			$this->assertEquals(true, $result);
		}
	}

	class SampleObject {
		private $val1, $val2, $val3;
		public function __construct($val1 = false, $val2 = false, $val3 = false) {
			$this->val1 = $val1;
			$this->val2 = $val2;
			$this->val3 = $val3;
		}
		public function fu() {
			return $this->val1 & $this->val2 & $this->val3;
		}
	}
}