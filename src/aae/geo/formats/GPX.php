<?php
/**
 *
 */
namespace aae\geo\formats {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\formats
	 */
	class GPX {
		protected $track;
		public $timeZone = 'UTC';
		public function __construct($trackInstance = null) {
			$this->track = $trackInstance;
		}
		
	
		/**
		 * __functionDescription__
		 */
		public function __toString() {
			$latFormat = "%01.9f";
			$lonFormat = "%01.9f";
			$eleFormat = "%01.1f";
			$timeFormat = "Y-m-d\TH:i:s\Z";

			$dom = new \DOMDocument(1.0, "UTF-8");
			$gpx = $dom->createElement("gpx");
			$gpx->setAttribute("version", "1.1");
			$gpx->setAttribute("creator", "Axel Ancona Esselmann - http://www.anconaesselmann.com");
			$gpx->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$gpx->setAttribute("xmlns", "http://www.topografix.com/GPX/1/1");
			$gpx->setAttribute("xsi:schemaLocation", "http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd");
			$gpx->setAttribute("xmlns:gpxtpx", "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
			$dom->appendChild($gpx);

			$gpxTrack = $dom->createElement("trk");
			$gpx->appendChild($gpxTrack);

			$trackName = $dom->createElement("name", $this->track->name);
			$gpxTrack->appendChild($trackName);
			if (count($this->track) > 0) {
				$time = new \DateTime();
				date_default_timezone_set($this->timeZone);
				$time->setTimezone(new \DateTimeZone($this->timeZone));
				$time->setTimestamp($this->track[0]->time);
				$trackTime = $dom->createElement("time", $time->format($timeFormat));
				$gpxTrack->appendChild($trackTime);

				$trackSegment = $dom->createElement("trkseg");
				$currentSegmentNumber = 1;
				foreach ($this->track as $key=>$trackPoint) {
					if (count($this->track->segmentStarts) > 1) {
						if ($key === $this->track->segmentStarts[$currentSegmentNumber]) {
							$gpxTrack->appendChild($trackSegment);
							$trackSegment = $dom->createElement("trkseg");
							$currentSegmentNumber++;
						}
					}

					$gpxPoint = $dom->createElement("trkpt");
					$gpxPoint->setAttribute("lat", sprintf($latFormat, $trackPoint->lat));
					$gpxPoint->setAttribute("lon", sprintf($lonFormat, $trackPoint->lon));
					$gpxPointElevation = $dom->createElement('ele', sprintf($eleFormat, $trackPoint->ele));
					$gpxPoint->appendChild($gpxPointElevation);
					$pointTime = new \DateTime();
					$pointTime->setTimestamp($trackPoint->time);
					$gpxPointTime = $dom->createElement('time', $pointTime->format($timeFormat));
					$gpxPoint->appendChild($gpxPointTime);
					$trackSegment->appendChild($gpxPoint);
				}
				$gpxTrack->appendChild($trackSegment);
			}

			$dom->formatOutput = TRUE;
			$formatted = $dom->saveXML();
			return $formatted;
		}
	}
}