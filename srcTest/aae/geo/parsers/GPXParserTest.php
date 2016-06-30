<?php
namespace aae\geo\parsers {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class GPXParserTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new GPXParser();
		}

		public function _getObject() {
			$obj = new GPXParser();
			return $obj;
		}
		
		public function test_parsing_a_track_with_one_segment() {
			// Setup
			$obj = $this->_getObject();
			$inputString = '<?xml version="1.0" encoding="UTF-8"?>
<gpx
	version="1.1"
	creator="RunKeeper - http://www.runkeeper.com"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns="http://www.topografix.com/GPX/1/1"
	xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd"
	xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1">
	<trk>
		<name><![CDATA[Running 2/1/14 10:15 am]]></name>
		<time>2014-02-01T10:15:08Z</time>
		<trkseg>
			<trkpt lat="37.760755000" lon="-122.486674000">
				<ele>70.0</ele>
				<time>2014-02-01T10:15:08Z</time>
			</trkpt>
			<trkpt lat="37.760847000" lon="-122.486735000">
				<ele>69.9</ele>
				<time>2014-02-01T10:15:11Z</time>
			</trkpt>
			<trkpt lat="37.760958000" lon="-122.486715000">
				<ele>69.8</ele>
				<time>2014-02-01T10:15:13Z</time>
			</trkpt>
			<trkpt lat="37.761075000" lon="-122.486695000">
				<ele>69.7</ele>
				<time>2014-02-01T10:15:16Z</time>
			</trkpt>
		</trkseg>
	</trk>
</gpx>
';
			$point1 = new \aae\math\geospatial\Point(37.760755000,-122.486674000);
			$point1->ele = 70.0;
			$point1->time = "2014-02-01T10:15:08Z";
			$point2 = new \aae\math\geospatial\Point(37.760847000,-122.486735000);
			$point2->ele = 69.9;
			$point2->time = "2014-02-01T10:15:11Z";
			$point3 = new \aae\math\geospatial\Point(37.760958000,-122.486715000);
			$point3->ele = 69.8;
			$point3->time = "2014-02-01T10:15:13Z";
			$point4 = new \aae\math\geospatial\Point(37.761075000,-122.486695000);
			$point4->ele = 69.7;
			$point4->time = "2014-02-01T10:15:16Z";

			$sequence = new \aae\math\geospatial\Sequence();
			$sequence->name = "Running 2/1/14 10:15 am";
			$sequence[] = $point1;
			$sequence[] = $point2;
			$sequence[] = $point3;
			$sequence[] = $point4;

			$tree = new \aae\adt\Tree("composite");
			$tree->append($sequence);

			// Testing
			$result = $obj->parseString($inputString);

			// TODO: Due to the static counting of tree nodes, this is awkward!!!!
			$this->_assertEqualTrees($tree, $result);
		}

		protected function _assertEqualTrees(\aae\adt\Tree $t1, \aae\adt\Tree $t2) {
			$kml1 = new \aae\geo\formats\KML($t1);
			$kml2 = new \aae\geo\formats\KML($t2);
			
			// Verification
			$this->assertEquals((string)$kml1, (string)$kml2);
		}


		public function test_parsing_a_track_with_multiple_segment() {
			// Setup
			$obj = $this->_getObject();
			$inputString = '<?xml version="1.0" encoding="UTF-8"?>
<gpx
	version="1.1"
	creator="RunKeeper - http://www.runkeeper.com"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns="http://www.topografix.com/GPX/1/1"
	xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd"
	xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1">
	<trk>
		<name><![CDATA[Running 2/1/14 10:15 am]]></name>
		<time>2014-02-01T10:15:08Z</time>
		<trkseg>
			<trkpt lat="37.760755000" lon="-122.486674000">
				<ele>70.0</ele>
				<time>2014-02-01T10:15:08Z</time>
			</trkpt>
			<trkpt lat="37.760847000" lon="-122.486735000">
				<ele>69.9</ele>
				<time>2014-02-01T10:15:11Z</time>
			</trkpt>
		</trkseg>
		<trkseg>
			<trkpt lat="37.760958000" lon="-122.486715000">
				<ele>69.8</ele>
				<time>2014-02-01T10:15:13Z</time>
			</trkpt>
		</trkseg>
		<trkseg>
			<trkpt lat="37.761075000" lon="-122.486695000">
				<ele>69.7</ele>
				<time>2014-02-01T10:15:16Z</time>
			</trkpt>
		</trkseg>
	</trk>
</gpx>
';
			$point1 = new \aae\math\geospatial\Point(37.760755000,-122.486674000);
			$point1->ele = 70.0;
			$point1->time = "2014-02-01T10:15:08Z";
			$point2 = new \aae\math\geospatial\Point(37.760847000,-122.486735000);
			$point2->ele = 69.9;
			$point2->time = "2014-02-01T10:15:11Z";
			$point3 = new \aae\math\geospatial\Point(37.760958000,-122.486715000);
			$point3->ele = 69.8;
			$point3->time = "2014-02-01T10:15:13Z";
			$point4 = new \aae\math\geospatial\Point(37.761075000,-122.486695000);
			$point4->ele = 69.7;
			$point4->time = "2014-02-01T10:15:16Z";

			$sequence = new \aae\math\geospatial\Sequence();
			$sequence->name = "Running 2/1/14 10:15 am";
			$sequence[] = $point1;
			$sequence[] = $point2;
			$sequence[] = $point3;
			$sequence[] = $point4;

			$tree = new \aae\adt\Tree("composite");
			$tree->append($sequence);

			// Testing
			$result = $obj->parseString($inputString);
			
			// Verification
			$this->_assertEqualTrees($tree, $result);
		}
	}


}