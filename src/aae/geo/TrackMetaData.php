<?php
/**
 *
 */
namespace aae\geo {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo
	 */
	class TrackMetaData {
		public $segmentTimes = array();
		public $segmentDistances = array();
		public $segmentElevationChanges = array();
		public $segmentSpeeds = array();

		protected $virtualVariables = array();

		private $plugins = array();

		private $calculator = null;

		public function __construct($track = null) {
			$this->calculator = new Calculator();
		}

		public function __get($varName) {
			if (array_key_exists($varName, $this->virtualVariables)) {
				return $this->virtualVariables[$varName];
			}
		}

		public function __set($varName, $value) {
			if (array_key_exists($varName, $this->virtualVariables)) {
				$this->virtualVariables[$varName] = $value;
			}
			return $this;
		}

		public function addPlugin($plugin) {
			$plugin->metaDataObject = $this;
			$this->plugins[get_class($plugin)] = $plugin;
			if (is_array($plugin->varName)) {
				foreach ($plugin->varName as $varName) {
					$this->virtualVariables[$varName] = null;
				}
			} else {
				$this->virtualVariables[$plugin->varName] = null;
			}
		}

		/**
		 * __functionDescription__
		 * @param __type__ __parameterDescription__
		 */
		public function reCalculate($track) {
			if (get_class($track) == "aae\adt\Tree") {
				$track = $track->getNode(0);
				// TODO: make sure that all subtracts are calculated!
			}
			$segment = 1;
			foreach ($this->plugins as $plugin) {
				$plugin->initialize();
			}
			for ($i=1; $i < count($track); $i++) {
				if (count($track->segmentStarts) - 1 >= $segment) {
					if ($track->segmentStarts[$segment] == $i) {
						$segment++;
						continue;
					}
				}
				$time = $this->calculator->timePassedBetweenCoordinates($track[$i - 1], $track[$i]);
				$this->segmentTimes[] = $time;

				$distance = $this->calculator->distanceBetweenCoordinates($track[$i - 1], $track[$i]);
				$this->segmentDistances[] = $distance;

				$speed = ($time > 0) ? $distance / ($time / (60 * 60)) : 0;
				$this->segmentSpeeds[] = $speed;

				$elevationChange = $this->calculator->elevationChangeBetweenCoordinates($track[$i - 1], $track[$i]);
				$this->segmentElevationChanges[] = $elevationChange;

				foreach ($this->plugins as $plugin) {
					$plugin->execute();
				}
			}
			foreach ($this->plugins as $plugin) {
				$plugin->finalize();
			}
		}
	}
}