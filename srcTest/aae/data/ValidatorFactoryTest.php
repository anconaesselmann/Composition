<?php
namespace aae\data {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ValidatorFactoryTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$className = 'aae\\data\\DummyClass';
			$instance = new \stdClass();
			$expected = $className."Val";
			$obj = new ValidatorFactory();
			$result = get_class($obj->build($className, $instance));
			$this->assertEquals($expected, $result);
		}

		public function test___construct_exception_not_enough_arguments() {
			try {
				$obj = new ValidatorFactory();
				$obj->build('');
			} catch (\Exception $e) {
				$this->assertEquals(211141637, $e->getCode());
				return;
			}
			$this->fail("An Exception should have been thrown, since the constructor takes at least one argument.");
		}
		
	}

	class DummyClassVal {
		public function __construct() {}
	}
}