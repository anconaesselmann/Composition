<?php
/**
 *
 */
namespace aae\track {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\track
	 */
	class Hiking extends Activity {
        protected $_distanceMiles;
        protected $_time;
        public function __construct($id, $dateTime, $distanceMiles = NULL, $time = NULL) {
            parent::__construct($id, $dateTime);
            $this->_distanceMiles = $distanceMiles;
            $this->_time          = $time;
        }
    }
}