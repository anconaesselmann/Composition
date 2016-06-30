<?php
/**
 *
 */
namespace aae\geo\meta {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo\meta
	 */
	class TotalElevation {
		public $varName = array("totalElevationGained", "totalElevationLost");
		public $metaDataObject = null;

		public function initialize() {
			
		}
		
		public function execute() {
			$meta = $this->metaDataObject;
			$var1 = $this->varName[0];
			$var2 = $this->varName[1];

			$elevationChange = $meta->segmentElevationChanges[count($meta->segmentElevationChanges) - 1];

			if ($elevationChange > 0) {
				$meta->$var1 += $elevationChange;
			} else {
				$meta->$var2 += $elevationChange;
			}
			
		}

		public function finalize() {
			
		}
	}
}