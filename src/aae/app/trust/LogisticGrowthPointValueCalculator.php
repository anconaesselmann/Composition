<?php
/**
 *
 */
namespace aae\app\trust {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\app\trust
	 */
	class LogisticGrowthPointValueCalculator implements PointValueCalculatorInterface {
            private $_bareValue;
            private $_maxScoreFactor;
            private $_scoreGrowthFactor;
            private $_scoreInitialGrowth;
            private $_maxPointsFactor;
            private $_pointsGrowthFactor;
            private $_pointsInitialGrowth;

        public function __construct(
            $bareValue,
            $maxScoreFactor,
            $scoreGrowthFactor,
            $scoreInitialGrowth,
            $maxPointsFactor,
            $pointsGrowthFactor,
            $pointsInitialGrowth
        ) {
            $this->_bareValue           = $bareValue;
            $this->_maxScoreFactor      = $maxScoreFactor;
            $this->_scoreGrowthFactor   = $scoreGrowthFactor;
            $this->_scoreInitialGrowth  = $scoreInitialGrowth;
            $this->_maxPointsFactor     = $maxPointsFactor;
            $this->_pointsGrowthFactor  = $pointsGrowthFactor;
            $this->_pointsInitialGrowth = $pointsInitialGrowth;
        }

        public function getPointValue($personalScore, $pointsInvested) {
            $scoreFactor  = $this->_calculateFactor($this->_maxScoreFactor,  $this->_scoreGrowthFactor,  $this->_scoreInitialGrowth,  $personalScore);
            $pointsFactor = $this->_calculateFactor($this->_maxPointsFactor, $this->_pointsGrowthFactor, $this->_pointsInitialGrowth, $pointsInvested);
            return $this->_bareValue * $scoreFactor * $pointsFactor;
        }
        private function _getShift($max, $growth, $initialGrowth) {
            $ln = (
                ($max - 1) /
                ($initialGrowth)
            ) - 1;
            return - $growth * log($ln);
        }
        public function _calculateFactor($max, $growth, $initialGrowth, $input) {
            $shift  = $this->_getShift($max, $growth, $initialGrowth);
            $offset = 1 - $initialGrowth;
            return  (
                ($max - $offset) /
                (
                    1 + exp(
                        - ($input + $shift) /
                        $growth
                    )
                )
            ) + $offset;
        }
	}
}