<?php
/**
 *
 */
namespace aae\geo\parsers {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\parsers
	 */
	class KmlParser {

		public static function parseString($inputString) {
			$xml = new \DOMDocument('1.0');
			// $dom->recover = TRUE;
			$xml->loadXML($inputString);
			#@$xml = simplexml_load_string($inputString);

			// $xml = simplexml_load_string($inputString, "SimpleXMLElement", LIBXML_NOERROR |  LIBXML_ERR_NONE);
			if ($xml === false) {
				throw new \Exception("Error parsing XML", 404141238);
			}
			$tree = new \aae\adt\Tree;
			$folders = $xml->getElementsByTagName( "Folder" );
			foreach ($folders as $folder) {
				$placemarks = $folder->getElementsByTagName( "Placemark" );
				foreach ($placemarks as $placemark) {
					$lineStrings = $folder->getElementsByTagName( "LineString" );
					foreach ($lineStrings as $lineString) {

						$sequence = new \aae\math\geospatial\Sequence();

						$coords = $placemark->getElementsByTagName( "coordinates" );

		#DANGEROUS! SWALLOWS ERRORS! TEMPORARY FIX
						if (is_null($coords->item(0))) {
							$this->errors = true;
							break;
						}
						$coord = explode(" ", $coords->item(0)->nodeValue);


						for ($i = 0; $i < count($coord); $i++) {
							$pieces = explode(",", trim($coord[$i]));
							if (count($pieces) === 3) {
								$point = new \aae\math\geospatial\Point($pieces[1], $pieces[0], $pieces[2]);
								$sequence->addElement($point);
							}
						}
						$tree->append($sequence);
					}

				}

			}
			return $tree;
		}

	}
}
