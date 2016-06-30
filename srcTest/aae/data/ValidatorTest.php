<?php
namespace aae\data {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ValidatorTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$instance = null;
			$dataType = null;
			$strict = true;
			$obj = new Validator($dataType, $instance, $strict);
		}

		/**
		 * TEST_DESCRIPTION
		 */
		public function test_validate() {
			// Setup
			$instance = "testString";
			$expected = $instance;
			$dataType = null;
			$mockDataValidator = $this->getMock("\\aae\\data\\ValidatorInterface");
			$mockDataValidator->expects($this->any())
             	->method('validate')
             	->will($this->returnValue(true));
			$mockValidatorFactory = $this->getMock('\\aae\\std\\SimpleFactory');
			$mockValidatorFactory->expects($this->once())
				->method('build')
				->will($this->returnValue($mockDataValidator));
			$obj = new Validator($dataType, $instance);
			$obj->validatorFactory = $mockValidatorFactory;
		
			// Testing
			$result = $obj->validate();
			
			// Verification
			$this->assertEquals($expected, $result);
		}
		
	}
}