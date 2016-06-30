<?php
/**
 *
 */
namespace aae\math\geospatial {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\math\geospatial
	 */
	class Sequence extends \aae\math\general\Sequence {
		public $name, $segmentStarts = array(0);

        public function getSegmentNbr() {
            $key = $this->key();
            $segmentNbr = 0;
            for ($i=0; $i < count($this->segmentStarts); $i++) {
                if ($key < $this->segmentStarts[$i]) return $segmentNbr;
                $segmentNbr = $i;
            }
            return $segmentNbr;
        }
	}
}