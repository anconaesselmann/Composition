<?php
namespace aae\geo\meta {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TotalElevationTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test___construct() {
			$obj = new TotalElevation();
		}
		
		/**
		 * TEST_DESCRIPTION
		 * 
		 * @dataProvider provider_totalTrackElevation
		 */
		public function test_totalTrackElevation($dir, $round, $expectedElevationGain, $expectedElevationLost) {
			// Setup
			$obj = new \aae\geo\TrackMetaData();
			$plugin = new \aae\geo\meta\TotalElevation();
			$obj->addPlugin($plugin);
			$cont   = $this->getCommonTestDataContent($dir);
			$track  = \aae\geo\parsers\GPXParser::parseString($cont);
		
			// Testing
			$obj->reCalculate($track);

			$resultElevationGain = $obj->totalElevationGained;
			$resultElevationLost = $obj->totalElevationLost;
			
			// Verification
			$this->assertEquals($expectedElevationGain, round($resultElevationGain, $round));
			$this->assertEquals($expectedElevationLost, round($resultElevationLost, $round));

		}

		public function provider_totalTrackElevation() {
			return array(
				array("Track1.gpx", 1, 266.3, -266.3),
				array("Track2.gpx", 1, 0.3, -.5),
			);
	    }
	}
}