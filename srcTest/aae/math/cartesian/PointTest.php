<?php
namespace aae\math\cartesian {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class PointTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Point(1, 2, 3);
		}
		
		public function provider___toString() {
			return array(
				array(1,2,3, "(1, 2, 3)"),
				array(1,2, null, "(1, 2)"),
			);
		}
		
		/**
		 * @dataProvider provider___toString
		 */
		public function test___toString($x, $y, $z, $expected) {
			# Given x, y, z coordinates
			$obj = new Point($x, $y, $z);
		
			# When the parameters are passed to the constructor
			$result = (string)$obj;
			
			# Then the string representation of the point is in the form
			# (x, y, z)
			$this->assertEquals($expected, $result);
		}

		
		/**
		 * @dataProvider provider___toString
		 */
		public function test_iteratorInterface($x, $y, $z, $expected) {
			# Given a point with initial values
			$obj = new Point($x, $y, $z);
		
			# When __get is called
			$result = array();
			foreach ($obj as $value) {
				$result[] = $value;
			}
			$result = "(" . implode(", ", $result) . ")";
			
			# Then the appropriate field is returned
			$this->assertEquals($expected, $result);
		}

		/**
		 * @dataProvider provider___toString
		 */
		public function test___getr($x, $y, $z, $expected) {
			# Given a point with initial values
			$obj = new Point($x, $y, $z);
		
			# When __get is called
			$result = "(";
			$result .= $obj->x . ", ";
			$result .= $obj->y;
			if ($obj->z !== null) {
				$result .= ", " . $obj->z;
			}
			$result .= ")";
			
			# Then the appropriate field is returned
			$this->assertEquals($expected, $result);
		}
	}
}