<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class HtmlEscaperTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new HtmlEscaper();
		}
		
	}
}