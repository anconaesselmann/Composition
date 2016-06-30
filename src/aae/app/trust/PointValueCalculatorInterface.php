<?php
/**
 *
 */
namespace aae\app\trust {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app\trust
	 */
	interface PointValueCalculatorInterface {
        public function getPointValue($personalScore, $pointsInvested);
	}
}