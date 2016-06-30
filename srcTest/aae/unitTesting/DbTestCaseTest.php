<?php
namespace aae\unitTesting {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	/**
	 * @group database
	 */
	class DbTestCaseTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$this->setUp();
			//$obj = new DbTestCase();
		}

	}
}