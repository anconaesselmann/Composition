<?php
namespace aae\connect {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class REQUESTTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new REQUEST();
		}
		
	}
}