<?php
namespace aae\ui {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class JsonEscaperTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new JsonEscaper();
		}
		
	}
}