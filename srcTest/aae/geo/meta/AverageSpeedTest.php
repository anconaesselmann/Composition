<?php
namespace aae\geo\meta {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class AverageSpeedTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;

		public function test___construct() {
			$obj = new AverageSpeed();
		}

		/**
	     * TEST_DESCRIPTION
	     * 
		 * @dataProvider provider_plugin_averageTrackSpeed
	     */
	    public function test_plugin_averageTrackSpeed($dir, $round, $expected) {
			// Setup
			$obj    = new \aae\geo\TrackMetaData();
			$plugin = new \aae\geo\meta\AverageSpeed();
			$obj->addPlugin($plugin);
			$cont   = $this->getCommonTestDataContent($dir);
			$track  = \aae\geo\parsers\GPXParser::parseString($cont);
			//var_dump($track);
			// Testing
			$obj->reCalculate($track);

			$varName = $plugin->varName;
			$result  = $obj->$varName;
			
			// Verification
			$this->assertEquals($expected, round($result, $round));
	    }

	    public function provider_plugin_averageTrackSpeed() {
			return array(
				array('Track1.gpx', 2, 11.38),
				array('Track2.gpx', 2, 2.34),
			);
	    }
		
	}
}