<?php
namespace aae\log {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ScreenLoggerTest extends \PHPUnit_Framework_TestCase {
		public function test_log_html() {
			$expectedRegEx = '/.*Type:.*0.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*\'test\'/';
			$obj = new ScreenLogger();
			ob_start();
			$obj->log("test");
			$result = ob_get_contents();
			ob_clean();
			$this->assertRegExp($expectedRegEx, $result);
		}

		public function test_log_text() {
			$expectedRegEx = '/.*Type:.*0.*Time:.*\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}.*Message:.*\'test\'/';
			$expected = "Type: 0, Message: 'test'\n";
			$obj = new ScreenLogger(false);
			ob_start();
			$obj->log("test");
			$result = ob_get_contents();
			ob_clean();
			$this->assertRegExp($expectedRegEx, $result);
		}
		
	}
}