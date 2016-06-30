<?php
namespace aae\geo\projection {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class MercatorTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Mercator();
		}
		
		public function provider_map() {
			return array(
				array(new \aae\math\geospatial\Point(60, -130), new \aae\math\cartesian\Point(-14455340.463793, 8390338.761308)),
				array(new \aae\math\geospatial\Sequence(new \aae\math\geospatial\Point(60, -130)), new \aae\math\cartesian\Sequence(new \aae\math\cartesian\Point(-14455340.463793, 8390338.761308))),
				array(
					new \aae\math\geospatial\Sequence(
						new \aae\math\geospatial\Point(60, -130),
						new \aae\math\geospatial\Point(61, -131)
					), 
					new \aae\math\cartesian\Sequence(
						new \aae\math\cartesian\Point(-14455340.463793, 8390338.761308),
						new \aae\math\cartesian\Point(-14566535.390437, 8616171.087027)
					))
			);
		}
		
		/**
		 * @dataProvider provider_map
		 */
		public function test_map($point, $expected) {
			# Given a geospacial point
			$obj = new Mercator();
		
			# When map is called
			$result = (string)$obj->map($point);
			
			# Then a cartesian point is retuned
			$this->assertEquals($expected, $result);
		}

	}
}