<?php
/**
 *
 */
namespace aae\math\cartesian {
	/**
	 * @author Axel Ancona Esselmann
	 * @package math\cartesian
	 */
	class Line {
		protected $_a, $_b;
		public function __construct(Point $a, Point $b) {
			$this->_a = $a;
			$this->_b = $b;
		}
		public function getA() {
			return $this->_a;
		}
		public function getB() {
			return $this->_b;
		}
	}
}