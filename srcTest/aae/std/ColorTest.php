<?php
namespace aae\std {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ColorTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Color();
		}
		
	}
}