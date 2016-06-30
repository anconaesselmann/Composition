<?php
namespace aae\geo\meta {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TotalDistanceTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test___construct() {
			$obj = new TotalDistance();
		}

		/**
	     * TEST_DESCRIPTION
	     * 
		 * @dataProvider provider_plugin_TotalDistance
	     */
	    public function test_plugin_TotalDistance($dir, $round, $expected) {
			// Setup
			$obj = new \aae\geo\TrackMetaData();
			$plugin = new \aae\geo\meta\TotalDistance();
			$obj->addPlugin($plugin);
			$cont   = $this->getCommonTestDataContent($dir);
			$track  = \aae\geo\parsers\GPXParser::parseString($cont);
		
			// Testing
			$obj->reCalculate($track);

			$varName = $plugin->varName;
			$result = $obj->$varName;
			
			// Verification
			$this->assertEquals($expected, round($result, $round));
		}

		public function provider_plugin_TotalDistance() {
			return array(
				array("Track1.gpx", 1, 10.2),
				array("Track2.gpx", 5, 0.04561+0.02445+0.02046),
			);
	    }
		
	}
}