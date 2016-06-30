<?php
namespace aae\log {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class AbstractLoggerTest extends \PHPUnit_Framework_TestCase {
		public function test_exception_logging() {
			$expectedRegEx = '/.*Type:.*2.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*Exception.*\'test\'/';
			$obj = new StringLogger();
			try {
				throw new \Exception("test", 1);
			} catch (\Exception $e) {
				$obj->logError($e);
			}
			$result = $obj->getLog();
			$this->assertRegExp($expectedRegEx, $result);
		}
		public function test_exception_logging_debug() {
			$expectedRegEx = '/.*Type:.*2.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*Exception.*\'test\'/';
			$obj = new StringLogger();
			$obj->debug();
			try {
				throw new \Exception("test", 1);
			} catch (\Exception $e) {
				$obj->logError($e);
				//print_r($obj->getLog());
			}
			$result = $obj->getLog();
			$this->assertRegExp($expectedRegEx, $result);
		}
		
	}
}