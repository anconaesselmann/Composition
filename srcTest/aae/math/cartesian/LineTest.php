<?php
namespace aae\math\cartesian {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class LineTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$a = new Point(1,2);
			$b = new Point(3,4);
			$obj = new Line($a, $b);
		}
		
	}
}