<?php
namespace aae\track {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class WalkingTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Walking();
		}
		
	}
}