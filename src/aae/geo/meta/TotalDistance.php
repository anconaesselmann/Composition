<?php
/**
 *
 */
namespace aae\geo\meta {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\meta
	 */
	class TotalDistance {
		public $varName = "totalDistance";
		public $metaDataObject = null;

		public function initialize() {
			
		}
		
		public function execute() {
			$meta = $this->metaDataObject;
			$var = $this->varName;
			$distance = $meta->segmentDistances[count($meta->segmentDistances) - 1];

			$meta->$var += $distance;
		}

		public function finalize() {
			
		}
	}
}