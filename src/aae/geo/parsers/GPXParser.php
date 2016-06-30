<?php
/**
 *
 */
namespace aae\geo\parsers {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\parsers
	 */
	class GPXParser {

		public static function parseFile($dir) {
			$contents = file_get_contents($dir);
			$track = self::parseString($contents);
			return $track;
		}

		/**
		 * Takes a string that contains valid GPX and returns a Geo object.
		 * @param string GPX string
		 */
		public static function parseString($inputString) {
			$xml = simplexml_load_string($inputString);

			if ($xml === false) {
				throw new \Exception("Error parsing XML", 404141238);
			}
			$elements = $xml->children();
			foreach ($elements as $element) {
				if ($element->getName() == "trk") {
					$xmlTrack = $element->children();

					$track = new \aae\math\geospatial\Sequence();
					$track->name = $xmlTrack->name->__toString();
					self::_parseTrkseg($xmlTrack, $track);
				}

			}
			#return $track;
			$tree = new \aae\adt\Tree("composite");
			$tree->append($track);
			return $tree;
		}

		protected static function _parseTrkseg($xmlTrack, $track) {
			foreach ($xmlTrack as $key => $xmlTrackSegment) {
				if ($key == "trkseg") {
					if (count($track) > 0) {
						$track->segmentStarts[] = count($track);
					}
					$xmlTrackPoints = $xmlTrackSegment->trkpt;
					self::_parsePoint($xmlTrackPoints, $track);
				}
			}
		}

		protected static function _parsePoint($xmlTrackPoints, $track) {
			foreach ($xmlTrackPoints as $xmlTrackPoint) {
				$attributes = $xmlTrackPoint->attributes;

				$point = new \aae\math\geospatial\Point();
				foreach ($xmlTrackPoint->children() as $key => $value) {
					if ($key == "ele") {
						$point->ele = floatval($value);
					}
					if ($key == "time") {
						$point->time = $value;
					}
				}
				foreach ($xmlTrackPoint->attributes() as $key => $value) {
					if ($key == "lat") {
						$point->lat = floatval($value);
					}
					if ($key == "lon") {
						$point->lon = floatval($value);
					}
				}
				$track[] = $point;
			}
		}
	}
}