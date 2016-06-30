<?php
namespace aae\geo {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TrackTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Track();
		}

		public function _getInstance() {
			$obj = new Track();
			return $obj;
		}
		
		public function test_array_access_add_to_end_of_array() {
			$point1 = new \aae\math\geospatial\Point(1.1, 2.2);
			$point2 = new \aae\math\geospatial\Point(3.3, 4.4);
			$point3 = new \aae\math\geospatial\Point(5.5, 6.6);

			$obj = $this->_getInstance();
			$obj[] = $point1;
			$obj[] = $point2;
			$obj[] = $point3;

			$this->assertEquals($point1, $obj[0]);
			$this->assertEquals($point2, $obj[1]);
			$this->assertEquals($point3, $obj[2]);

			$this->assertEquals(3, count($obj));
		}

		public function test_itteration() {
			$point1 = new \aae\math\geospatial\Point(1.1, 2.2);
			$point2 = new \aae\math\geospatial\Point(3.3, 4.4);
			$point3 = new \aae\math\geospatial\Point(5.5, 6.6);

			$obj = $this->_getInstance();
			$obj[] = $point1;
			$obj[] = $point2;
			$obj[] = $point3;

			$counter = 0;
			foreach ($obj as $key => $value) {
				$counter++;
				$pointerName = "point".$counter;
				$this->assertEquals($$pointerName, $value);
			}
			$this->assertEquals(3, $counter);
		}
	}
}