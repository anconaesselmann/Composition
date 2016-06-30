<?php
namespace aae\geo\parsers {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class KmlParserTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new KmlParser();
		}

		/*public function test_parseString() {
			# Given
			$inputString = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
  <Document>
    <name>Running 2/4/14 7:55 pm</name>
    <open>1</open>
    <Folder>
      <name>Tracks</name>
      <open>1</open>
      <Placemark>
        <name>Running 2/4/14 7:55 pm</name>
        <visibility>1</visibility>
        <LineString>
          <tessellate>1</tessellate>
          <coordinates>-122.513569,37.778194,15.5 -122.513545,37.778101,14.1 -122.513511,37.77801,13.1</coordinates>
        </LineString>
      </Placemark>
    </Folder>
  </Document>
</kml>
';
			$obj = new KmlParser();
		
			# When parseString is called
			$result = $obj->parseString($inputString);
			
			//var_dump($result);
			# Then 
			$expected = "";
			$this->assertEquals($expected, $result);
		}*/
		
	}
}