<?php
namespace aae\geo\meta {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TotalTimeTest extends \PHPUnit_Framework_TestCase {
		use \aae\unitTesting\TestFilesTrait;
		
		public function test___construct() {
			$obj = new TotalTime();
		}

	    /**
	     * TEST_DESCRIPTION
	     * 
		 * @dataProvider provider_plugin_TotalTime
	     */
	    public function test_plugin_TotalTime($dir, $expected) {
			// Setup
			$obj    = new \aae\geo\TrackMetaData();
			$plugin = new \aae\geo\meta\TotalTime();
			$obj->addPlugin($plugin);
			$cont   = $this->getCommonTestDataContent($dir);
			$track  = \aae\geo\parsers\GPXParser::parseString($cont);
		
			// Testing
			$obj->reCalculate($track);

			$varName = $plugin->varName;
			$result  = $obj->$varName;
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function provider_plugin_TotalTime() {
			return array(
				array("Track1.gpx", 3222),
				array("Track2.gpx", 8+123+8),
			);
	    }
		
	}
}