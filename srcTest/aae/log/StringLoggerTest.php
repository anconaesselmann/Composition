<?php
namespace aae\log {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class StringLoggerTest extends \PHPUnit_Framework_TestCase {
		public function test_log_html() {
			$expectedRegEx = '/.*Type:.*0.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*\'test\'/';
			$obj = new StringLogger();
			$obj->log("test");
			$result = $obj->getLog();
			$this->assertRegExp($expectedRegEx, $result);
		}
		/**
		 * TEST_DESCRIPTION
		 */
		public function test_logDebug() {
			$expectedRegEx = '/.*Type:.*1.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*\'test\'/';
			$obj = new StringLogger();
			$obj->debug();
			$obj->logDebug("test");
			$result = $obj->getLog();
			$this->assertRegExp($expectedRegEx, $result);
		}
	}
}