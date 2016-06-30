<?php
/**
 *
 */
namespace aae\geo\meta {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\meta
	 */
	class TotalTime {
		public $varName = "totalTime";
		public $metaDataObject = null;

		public function initialize() {
			
		}
		
		public function execute() {
			$meta = $this->metaDataObject;
			$var  = $this->varName;
			$time = $meta->segmentTimes[count($meta->segmentTimes) - 1];

			$meta->$var += $time;
		}

		public function finalize() {

		}
	}
}