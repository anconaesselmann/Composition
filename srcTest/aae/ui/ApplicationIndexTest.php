<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ApplicationIndexTest extends \PHPUnit_Framework_TestCase {
		public function testException___construct_with_no_config_file() {
			$expectedCode = 1015141705;
			try {
				$obj = new ApplicationIndex("");

				$this->fail("Expected \Exception with code $expectedCode containing \"$expectedMssg\"");
			} catch (\Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				$this->assertEquals($expectedCode, $code);
				return;
			}
		}

	}
}