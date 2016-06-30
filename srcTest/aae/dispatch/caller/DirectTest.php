<?php
namespace aae\dispatch\caller {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DirectTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Direct(null, null);
		}
		
	}
}