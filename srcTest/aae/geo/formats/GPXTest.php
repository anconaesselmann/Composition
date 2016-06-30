<?php
namespace aae\geo\formats {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class GPXTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new GPX();
		}

		public function _getInstance() {
			$obj = new GPX();
			return $obj;
		}

		public function test___toString() {
			$expected = '<?xml version="1" encoding="UTF-8"?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="Axel Ancona Esselmann - http://www.anconaesselmann.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1">
  <trk>
    <name>Running 2/1/14 10:15 am</name>
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

			$track = new \aae\geo\Track();
			$track->name = "Running 2/1/14 10:15 am";
			$track[] = $point1;
			$track[] = $point2;
			$track[] = $point3;
			$track[] = $point4;

			$gpx = new GPX($track);
			$result = $gpx->__toString();
			$this->assertEquals($expected, $result);
		}


		public function test___toString_multiple_track_segments() {
			$expected = '<?xml version="1" encoding="UTF-8"?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="Axel Ancona Esselmann - http://www.anconaesselmann.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1">
  <trk>
    <name>Running 2/1/14 10:15 am</name>
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

			$track = new \aae\geo\Track();
			$track->name = "Running 2/1/14 10:15 am";
			$track[] = $point1;
			$track[] = $point2;
			$track[] = $point3;
			$track[] = $point4;
			$track->segmentStarts[] = 2;
			$track->segmentStarts[] = 3;

			$gpx = new GPX($track);
			$result = $gpx->__toString();
			$this->assertEquals($expected, $result);
		}
		
	}
}