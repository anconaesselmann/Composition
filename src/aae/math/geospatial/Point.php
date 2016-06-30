<?php
/**
 *
 */
namespace aae\math\geospatial {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo
	 */
	class Point {
		protected $timeZone = 'UTC';

		protected $ele = 0;
		protected $time = null;
		protected $lat = null;
		protected $lon = null;

		public function __construct($lat = null, $lon = null, $ele = null, $time = null) {
			date_default_timezone_set($this->timeZone);
			$this->__set("lat", $lat);
			$this->__set("lon", $lon);
			$this->__set("ele", $ele);
			if ($time !== null) {
				$this->__set("time", $time);
			}
		}

		public function __set($property, $value) {
			switch ($property) {
				case 'lat':
					$this->lat = $value;
					break;
				case 'lon':
					$this->lon = $value;
					break;
				case 'ele':
					$this->ele = $value;
					break;
				case 'time':
					$this->time = new \DateTime($value);
					break;
				default:
					throw new \Exception("Trying to set property that does not exist: $property", 409141636);
					break;
			}
			return $this;
		}

		public function getDateTime() {
			return $this->time;
		}

		public function __get($property) {
			switch ($property) {
				case 'lat':
					$result = $this->lat;
					break;
				case 'lon':
					$result = $this->lon;
					break;
				case 'ele':
					$result = $this->ele;
					break;
				case 'time':
					if ($this->time != null) {
						$result = $this->time->getTimestamp();
					} else $result = null;
					break;
				default:
					throw new \Exception("Trying to get property that does not exist: $property", 409141637);
			}
			return $result;
		}

		public function __toString() {
			return $this->format();
		}

		public function format(
								$latFormat = "%01.9f,",
								$lonFormat = "%01.9f,",
								$timeZone = 'UTC',
								$timeFormat = "Y-m-d\TH:i:s\Z",
								$eleFormat = "%01.1f,") {
			$latStr = sprintf($latFormat, $this->lat);
			$lonStr = sprintf($lonFormat, $this->lon);
			$eleStr = sprintf($eleFormat, $this->ele);
			if ($this->time !== null) {
				$timeStr = $this->time->format($timeFormat);
			} else $timeStr = "";

			date_default_timezone_set($timeZone);
			$result = $latStr.$lonStr.$eleStr.$timeStr;
			return $result;
		}
	}
}