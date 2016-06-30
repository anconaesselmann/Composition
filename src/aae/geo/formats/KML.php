<?php
/**
 *
 */
namespace aae\geo\formats {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\formats
	 */
	class KML {
		protected $_dom, $_kmlDocument, $_folders;

		public $timeZone = 'UTC';
		public function __construct($trackInstance = null) {
			$this->_folders = [];
			$this->_kmlDocument = $this->_createKmlDocument();

			if (!is_null($trackInstance)) {
				$tracksFolder = $this->_createFolder($this->_kmlDocument, "Tracks", true);
				$toursFolder  = $this->_createFolder($this->_kmlDocument, "Tours", true);

				foreach ($trackInstance as $sequence) {
					$this->_parse($tracksFolder, $toursFolder, $sequence);
				}
			}
		}

		public function saveHtml() {
			$formatted = $this->_dom->saveXML();
			return $formatted;
		}

		private function _createKmlDocument() {
			$this->_dom = new \DOMDocument('1.0', "UTF-8");
			$this->_dom->formatOutput = TRUE;
			$gpx = $this->_dom->createElement("kml");

			$gpx->setAttribute("xmlns",      "http://www.opengis.net/kml/2.2");
			$gpx->setAttribute("xmlns:gx",   "http://www.google.com/kml/ext/2.2");
			$gpx->setAttribute("xmlns:kml",  "http://www.opengis.net/kml/2.2");
			$gpx->setAttribute("xmlns:atom", "http://www.w3.org/2005/Atom");

			$this->_dom->appendChild($gpx);

			$kmlDocument = $this->_dom->createElement("Document");
			$gpx->appendChild($kmlDocument);

			$openTag = $this->_dom->createElement("open", 1);
			$kmlDocument->appendChild($openTag);

			return $kmlDocument;
		}

		private function _getSubfolderName($folders, $index) {
			$result = $folders[0];
			for ($i=1; $i < $index; $i++) {
				$result .= "\\" . $folders[$i];
			}
			return $result;
		}

		private function _getParent($parent, $folderName, $open) {
			$folderNames = explode("\\", $folderName);
			if (count($folderNames) < 2) {
				return $parent;
			} else {
				$subFolderName = $this->_getSubfolderName($folderNames, count($folderNames) - 1);
				if (array_key_exists($subFolderName, $this->_folders)) {
					return $this->_folders[$subFolderName];
				} else {
					return $this->_createFolder($parent, $subFolderName, $open);
				}
			}
		}

		private function _createFolder($parent, $folderName, $open = true) {
			if (!array_key_exists($folderName, $this->_folders)) {
				$folderNames   = explode("\\", $folderName);
				$parent        = $this->_getParent($parent, $folderName, $open);
				$domFolder     = $this->_dom->createElement("Folder");
				$parent->appendChild($domFolder);
				$domFolderName = $this->_dom->createElement("name", $folderNames[count($folderNames) - 1]);
				$domFolder->appendChild($domFolderName);
				if ($open) {
					$openTag = $this->_dom->createElement("open", 1);
					$domFolder->appendChild($openTag);
				}
				$this->_folders[$folderName] = $domFolder;
			}
			return $this->_folders[$folderName];
		}

		public function addTrack($track, $tracksFolder, $toursFolder, $name = null) {
			foreach ($track as $sequence) {
				$this->_parse($tracksFolder, $toursFolder, $sequence, $name);
			}
		}

		protected function _parse($tracksFolder, $toursFolder, $sequence, $name = null) {
			if (!array_key_exists($tracksFolder, $this->_folders)) {
				$this->_createFolder($this->_kmlDocument, $tracksFolder);
			}
			if (!array_key_exists($toursFolder, $this->_folders)) {
				$this->_createFolder($this->_kmlDocument, $toursFolder);
			}
			$tracksFolder = $this->_folders[$tracksFolder];
			$toursFolder = $this->_folders[$toursFolder];

			$latFormat = "%01.9f";
			$lonFormat = "%01.9f";
			$eleFormat = "%01.1f";
			$timeFormat = "Y-m-d\TH:i:s\Z";
			if (get_class($sequence) == 'aae\math\geospatial\Sequence') {
				if (is_null($name)) {
					$name = $sequence->name;
				}
				$trackPlacemark = $this->_dom->createElement("Placemark");
				$tracksFolder->appendChild($trackPlacemark);

				$trackName = $this->_dom->createElement("name", $name);
				$trackPlacemark->appendChild($trackName);

				$visibilityTag = $this->_dom->createElement("visibility", 1);
				$trackPlacemark->appendChild($visibilityTag);

				$lineStringTag = $this->_dom->createElement("LineString");
				$trackPlacemark->appendChild($lineStringTag);

				$tessellateTag = $this->_dom->createElement("tessellate", 1);
				$lineStringTag->appendChild($tessellateTag);

				$trackSegmentString = "";
				foreach ($sequence as $trackPoint) {
					$trackSegmentString .= strval($trackPoint->lon).",".strval($trackPoint->lat).",".strval($trackPoint->ele)." ";
				}
				$trackSegment = $this->_dom->createElement("coordinates", $trackSegmentString);

				$lineStringTag->appendChild($trackSegment);

				// Tours folder


				$tourPlacemark = $this->_dom->createElement("Placemark");
				$toursFolder->appendChild($tourPlacemark);

				$tourName = $this->_dom->createElement("name", $name);
				$tourPlacemark->appendChild($tourName);

				$visibilityTag = $this->_dom->createElement("visibility", 1);
				$tourPlacemark->appendChild($visibilityTag);

				$gxTrackTag = $this->_dom->createElement("gx:Track");
				$tourPlacemark->appendChild($gxTrackTag);


				$time = new \DateTime();
				$time->setTimezone(new \DateTimeZone($this->timeZone));
				foreach ($sequence as $trackPoint) {
					$time->setTimestamp($trackPoint->time);
					$trackTime = $this->_dom->createElement("time", $time->format($timeFormat));
					$whereTag = $this->_dom->createElement("when", $time->format($timeFormat));
					$gxTrackTag->appendChild($whereTag);
				}
				foreach ($sequence as $trackPoint) {
					$whereTag = $this->_dom->createElement("gx:coord", strval($trackPoint->lon).",".strval($trackPoint->lat).",".strval($trackPoint->ele));
					$gxTrackTag->appendChild($whereTag);
				}
			} else {
				echo "\n!!!!!!!!!!!!!!".get_class($sequence)."\n";
				foreach ($sequence as $s) {
					$this->_parse($tracksFolder, $toursFolder, $s);
				}
			}
		}

		public function __toString() {
			return $this->saveHtml();
		}
	}
}