<?php
namespace aae\geo {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TrackMetaDataTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new TrackMetaData();
		}

		/**
		 * TEST_DESCRIPTION
		 * 
		 * @dataProvider provider_reCalculate
		 */
		public function test_reCalculate($dir, 
										 $expectedSegmentTimes, 
										 $distanceRound, $expectedSegmentDistances, 
										 $elevationRound, $expectedSegmentElevationChanges,
										 $speedRound, $expectedSegmentSpeeds) {
			// Setup
			$obj = new TrackMetaData();
			$track = \aae\geo\parsers\GPXParser::parseFile($dir);
		
			// Testing
			$obj->reCalculate($track->getNode(0));
			
			// Verification

			$segmentDistances = $obj->segmentDistances;
			for ($i=0; $i < count($segmentDistances); $i++) { 
				$segmentDistances[$i] = round($segmentDistances[$i], $distanceRound);
			}
			$segmentElevationChanges = $obj->segmentElevationChanges;
			for ($i=0; $i < count($segmentElevationChanges); $i++) { 
				$segmentElevationChanges[$i] = round($segmentElevationChanges[$i], $elevationRound);
			}
			$segmentSpeeds = $obj->segmentSpeeds;
			for ($i=0; $i < count($segmentSpeeds); $i++) { 
				$segmentSpeeds[$i] = round($segmentSpeeds[$i], $speedRound);
			}
			
			$this->assertEquals($expectedSegmentTimes,            $obj->segmentTimes);
			$this->assertEquals($expectedSegmentDistances,        $segmentDistances);
			$this->assertEquals($expectedSegmentElevationChanges, $segmentElevationChanges);
			$this->assertEquals($expectedSegmentSpeeds,           $segmentSpeeds);
		}

		public function provider_reCalculate() {
			return array(
				#array(dirname(__FILE__)."/CalculatorTestData/Track1.gpx", 11.38),
				array(dirname(__FILE__)."/CalculatorTestData/Track2.gpx", 
					array(1,3,2,2,7,9,107,4,4,0), 
					5, array(0.01077, 0.01373, 0.01010, 0.01100, 0.01054, 0.01010, 0.00381, 0.01032, 0.01014, 0.0),
					1, array(-0.1, 0.0, -0.1, 0.0, 0.0, 0.0, 0.0, 0.1, 0.2, -0.3),
					1, array(38.8, 16.5, 18.2, 19.8, 5.4, 4.0, 0.1, 9.3, 9.1, 0.0)
				),
			);
	    }

	}
}