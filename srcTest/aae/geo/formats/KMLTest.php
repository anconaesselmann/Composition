<?php
namespace aae\geo\formats {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class KMLTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new KML();
		}
		
				public function test___toString() {
			$expected = $inputString = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
  <Document>
    <open>1</open>
    <Folder>
      <name>Tracks</name>
      <open>1</open>
      <Placemark>
        <name>Test Track</name>
        <visibility>1</visibility>
        <LineString>
          <tessellate>1</tessellate>
          <coordinates>-122.486674,37.760755,70 -122.486735,37.760847,69.9 -122.486715,37.760958,69.8 -122.486695,37.761075,69.7 </coordinates>
        </LineString>
      </Placemark>
    </Folder>
    <Folder>
      <name>Tours</name>
      <open>1</open>
      <Placemark>
        <name>Test Track</name>
        <visibility>1</visibility>
        <gx:Track>
          <when>2014-02-01T10:15:08Z</when>
          <when>2014-02-01T10:15:11Z</when>
          <when>2014-02-01T10:15:13Z</when>
          <when>2014-02-01T10:15:16Z</when>
          <gx:coord>-122.486674,37.760755,70</gx:coord>
          <gx:coord>-122.486735,37.760847,69.9</gx:coord>
          <gx:coord>-122.486715,37.760958,69.8</gx:coord>
          <gx:coord>-122.486695,37.761075,69.7</gx:coord>
        </gx:Track>
      </Placemark>
    </Folder>
  </Document>
</kml>
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
      $sequence->name = "Test Track";
      $sequence[] = $point1;
      $sequence[] = $point2;
      $sequence[] = $point3;
      $sequence[] = $point4;

      $tree = new \aae\adt\Tree("composite");
      $tree->append($sequence);
      $tree->name = "Test Track";

			$gpx = new KML($tree);
			$result = $gpx->__toString();
			$this->assertEquals($expected, $result);
		}
	}
}