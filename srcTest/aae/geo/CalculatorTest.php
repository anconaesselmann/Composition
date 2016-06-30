<?php
namespace aae\geo {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class CalculatorTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Calculator();
		}


		public function provider_distVincenty() {
			return array(
				array(37.80633870963089, -122.4688674708928, 37.80633559063098, -122.4688715754226, 5,    0.00050),
				array(37.80633017858694, -122.468744100262,  37.80624490616218, -122.4686640203978, 5,    0.01180),
				array(37.82546785854429, -122.4789585763342, 37.81389283379842, -122.4776350353646, 5,    1.29002),
				array(32.72850529700872, -114.6156093648087, 41.99845694483191, -124.2115142556216, 2, 1332.71),
			);
	    }

	    /**
	     * @dataProvider provider_distVincenty
	     */
		public function test_distVincenty($lat1, $lon1, $lat2, $lon2, $decimal, $expected) {
			$result = Calculator::distVincenty($lat1, $lon1, $lat2, $lon2);
			$this->assertEquals($expected, round($result, $decimal));
		}


		public function provider_distVincenty_with_Point_arguments() {
			return array(
				array(new \aae\math\geospatial\Point(37.80633870963089, -122.4688674708928), new \aae\math\geospatial\Point(37.80633559063098, -122.4688715754226), 5,    0.00050),
				array(new \aae\math\geospatial\Point(37.80633017858694, -122.468744100262) , new \aae\math\geospatial\Point(37.80624490616218, -122.4686640203978), 5,    0.01180),
				array(new \aae\math\geospatial\Point(37.82546785854429, -122.4789585763342), new \aae\math\geospatial\Point(37.81389283379842, -122.4776350353646), 5,    1.29002),
				array(new \aae\math\geospatial\Point(32.72850529700872, -114.6156093648087), new \aae\math\geospatial\Point(41.99845694483191, -124.2115142556216), 2, 1332.71),
			);
	    }

	    /**
	     * @dataProvider provider_distVincenty_with_Point_arguments
	     */
		public function test_distVincenty_with_Point_arguments($point1, $point2, $decimal, $expected) {
			$result = Calculator::distanceBetweenCoordinates($point1, $point2);
			$this->assertEquals($expected, round($result, $decimal));
		}

		/**
		 * TEST_DESCRIPTION
		 * 
		 * @dataProvider provider_trackDistance
		 */
		public function test_trackDistance($dir, $rounding, $expected) {
			// Setup
			$track = \aae\geo\parsers\GPXParser::parseFile($dir);

			// Testing
			$result = Calculator::trackDistance($track->getNode(0));
			
			// Verification
			$this->assertEquals($expected, round($result, $rounding));
		}

		public function provider_trackDistance() {
			return array(
				array(dirname(__FILE__)."/CalculatorTestData/Track1.gpx", 1, 10.2),
				array(dirname(__FILE__)."/CalculatorTestData/Track2.gpx", 5, 0.04561+0.02445+0.02046),
			);
	    }

		/**
		 * Returns the elevation change from point1 to point2. If point1 is
		 * lower than point2 the value is positive, if point1 is higher than
		 * point2 the value is negative.
		 *
		 * @dataProvider provider_elevationChangeBetweenCoordinates
		 */
		public function test_elevationChangeBetweenCoordinates($point1, $point2, $expected) {
			// Testing
			$result = Calculator::elevationChangeBetweenCoordinates($point1, $point2);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function provider_elevationChangeBetweenCoordinates() {
			return array(
				array(new \aae\math\geospatial\Point(37.80633870963089, -122.4688674708928, 60.5), new \aae\math\geospatial\Point(37.80633559063098, -122.4688715754226, 47.2), -13.3),
				array(new \aae\math\geospatial\Point(37.80633017858694, -122.468744100262,  61.5), new \aae\math\geospatial\Point(37.80624490616218, -122.4686640203978, 66.6),   5.1),
				array(new \aae\math\geospatial\Point(37.82546785854429, -122.4789585763342,  0.0), new \aae\math\geospatial\Point(37.81389283379842, -122.4776350353646,  0.0),   0.0),
				array(new \aae\math\geospatial\Point(32.72850529700872, -114.6156093648087),       new \aae\math\geospatial\Point(41.99845694483191, -124.2115142556216),         0.0),
			);
	    }

	    /**
		 * TEST_DESCRIPTION
		 * 
		 * @dataProvider provider_totalTrackElevation
		 */
		public function test_totalTrackElevation($dir, $expected) {
			// Setup
			$track = \aae\geo\parsers\GPXParser::parseFile($dir);
			// Testing
			$result = Calculator::totalTrackElevation($track->getNode(0));
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function provider_totalTrackElevation() {
			return array(
				array(dirname(__FILE__)."/CalculatorTestData/Track1.gpx", array("elevGain" => 266.3, "elevLoss" => -266.3)),
				#array(dirname(__FILE__)."/CalculatorTestData/Track2.gpx", array("elevGain" => 266.3, "elevLoss" => -266.3)),
			);
	    }


		/**
		 * @dataProvider provider_timePassedBetweenCoordinates
		 */
		public function test_timePassedBetweenCoordinates($point1, $point2, $expected) {
			// Testing
			$result = Calculator::timePassedBetweenCoordinates($point1, $point2);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function provider_timePassedBetweenCoordinates() {
			return array(
				array(new \aae\math\geospatial\Point(37.80633870963089, -122.4688674708928, 60.5, "2014-02-04T19:55:19Z"), new \aae\math\geospatial\Point(37.80633559063098, -122.4688715754226, 47.2, "2014-02-04T19:55:23Z"), 4),
				array(new \aae\math\geospatial\Point(37.80633017858694, -122.468744100262,  61.5, "2014-02-04T19:55:23Z"), new \aae\math\geospatial\Point(37.80624490616218, -122.4686640203978, 66.6, "2014-02-04T19:56:36Z"), 73),
				array(new \aae\math\geospatial\Point(32.72850529700872, -114.6156093648087),       new \aae\math\geospatial\Point(41.99845694483191, -124.2115142556216),         0.0),
			);
	    }

	    public function test_timePassedBetweenCoordinates_exception_when_out_of_order() {
			// Setup
			$point1 = new \aae\math\geospatial\Point(37.82546785854429, -122.4789585763342,  0.0, "2014-02-04T19:56:36Z");
			$point2 = new \aae\math\geospatial\Point(37.81389283379842, -122.4776350353646,  0.0, "2014-02-04T19:56:32Z");

			// Testing
			try {
				$result = Calculator::timePassedBetweenCoordinates($point1, $point2);
			} catch (\Exception $e) {
				return;
			}
			$this->fail("An exception should have been thrown.");
		}

		/**
		 * TEST_DESCRIPTION
		 * 
		 * @dataProvider provider_totalTrackTime
		 */
		public function test_totalTrackTime($dir, $expected) {
			// Setup
			$track = \aae\geo\parsers\GPXParser::parseFile($dir);
			// Testing
			//echo "here\n";
			$track = $track->getNode(0);
			//var_dump($track);
			//echo get_class($track)."\n";
			foreach ($track as $key => $value) {
			//	echo get_class($value)."\n";
			}
			//echo "end\n";
			$result = Calculator::totalTrackTime($track);
			
			// Verification
			$this->assertEquals($expected, $result);
		}

		public function provider_totalTrackTime() {
			return array(
				array(dirname(__FILE__)."/CalculatorTestData/Track1.gpx", 3222),
				array(dirname(__FILE__)."/CalculatorTestData/Track2.gpx", 8+123+8),
			);
	    }

	    /**
	     * TEST_DESCRIPTION
	     * 
		 * @dataProvider provider_averageTrackSpeed
	     */
	    public function test_averageTrackSpeed($dir, $expected) {
	    	// Setup
	    	$track = \aae\geo\parsers\GPXParser::parseFile($dir);

	    	// Testing
	    	$result = Calculator::averageTrackSpeed($track->getNode(0));
	    	
	    	// Verification
	    	$this->assertEquals($expected, round($result, 2));
	    }

	    public function provider_averageTrackSpeed() {
			return array(
				array(dirname(__FILE__)."/CalculatorTestData/Track1.gpx", 11.38),
				array(dirname(__FILE__)."/CalculatorTestData/Track2.gpx", 2.34),
			);
	    }
		
	}
}