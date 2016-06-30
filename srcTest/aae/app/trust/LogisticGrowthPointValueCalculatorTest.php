<?php
namespace aae\app\trust {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class LogisticGrowthPointValueCalculatorTest extends \PHPUnit_Framework_TestCase {

        public function test_getPointValue() {
            # Given
            $personalScore   = 1000 * 60 * 60 * 24 * 365;
            $pointsInvested  = 50;

            $baseValue       = 60 * 60 * 24 * 30;

            $maxScore        = 5;
            $maxPoints       = 5;
            $growthScore     = 4500000000;
            $growthPoints    = 7;
            $initGrowthScore = 0.01;
            $sut = new LogisticGrowthPointValueCalculator(
                $baseValue,
                $maxScore,
                $growthScore,
                $initGrowthScore,
                $maxPoints,
                $growthPoints,
                $initGrowthScore
            );

            # When getPointValue is called
            // for ($i=0; $i < 10000000; $i++) {
            //     $result = round($sut->getPointValue($personalScore, $pointsInvested)*1000)/1000;
            // }
            $result = round($sut->getPointValue($personalScore, $pointsInvested)*1000)/1000;

            # Then
            $expected = 41206238.684;
            $this->assertEquals($expected, $result);
        }

	}
}