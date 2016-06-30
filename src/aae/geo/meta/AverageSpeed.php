<?php
/**
 *
 */
namespace aae\geo\meta {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\meta
	 */
	class AverageSpeed {
		public $varName = "averageSpeed";
		public $metaDataObject = null;

		public function initialize() {
			$meta           = $this->metaDataObject;
			$distancePlugin = new \aae\geo\meta\TotalDistance();
			$meta->addPlugin($distancePlugin);
			$timePlugin     = new \aae\geo\meta\TotalTime();
			$meta->addPlugin($timePlugin);
		}
		
		public function execute() {

		}

		public function finalize() {
			$meta = $this->metaDataObject;
			$var = $this->varName;

			$distance = $meta->totalDistance;
			$time = $meta->totalTime;
			if ($time > 0) {
				$meta->$var += $distance / ($time / (60 * 60));
			}
		}
	}
}