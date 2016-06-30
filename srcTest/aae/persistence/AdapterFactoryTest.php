<?php
namespace aae\persistence {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class AdapterFactoryTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new AdapterFactory();
		}

		/**
		 * TEST_DESCRIPTION
		 */
		public function test_build() {
			// Setup
			$obj = new AdapterFactory();
			$adapterName = 'stdClass';
			$expected = $adapterName;
		
			// Testing
			$result = get_class($obj->build($adapterName));
			
			// Verification
			$this->assertEquals($expected, $result);
		}
		
	}
}