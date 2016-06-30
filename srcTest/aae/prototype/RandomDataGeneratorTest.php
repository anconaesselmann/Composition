<?php
namespace aae\prototype {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class _RandomDataGeneratorTest_RandomNbrGeneratorMock {
        public function normalInt($average, $stdDev) {
            return $average;
        }
    }
    class RandomDataGeneratorTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new RandomDataGenerator();
		}

        public function setUp() {
            $this->sut = new RandomDataGenerator(new _RandomDataGeneratorTest_RandomNbrGeneratorMock());
        }

        public function test_getSentence() {
            # Given SETUP_CONDITIONS
            $average = 14;
            $stdDev = 3;
            # When
            $result = $this->sut->getSentence($average, $stdDev);

            $this->assertEquals($average, count(explode(' ', $result)));
        }
        public function test_getParagraph() {
            # Given SETUP_CONDITIONS
            $averageS = 14;
            $stdDevS = 3;
            $averageP = 5;
            $stdDevP = 3;

            # When
            $result = $this->sut->getParagraph($averageP, $stdDevP, $averageS, $stdDevS);

            # Then
            $this->assertEquals($averageS * $averageP, count(explode(' ', $result)));
        }
        public function test_getText() {
            # Given
            $averageS = 14;
            $stdDevS = 3;
            $averageP = 5;
            $stdDevP = 3;
            $averageT = 4;
            $stdDevT = 3;

            # When
            $result = $this->sut->getText($averageT, $stdDevT, $averageP, $stdDevP, $averageS, $stdDevS);

            # Then
            $this->assertEquals($averageT, count(explode("\n", $result)));
        }
        public function test_getWords() {
            # Given
            $nbrWords = 4;
            # When
            $result = $this->sut->getWords($nbrWords);

            # Then
            $this->assertEquals($nbrWords, count(explode(" ", $result)));
        }
        public function test_getSentences() {
            # Given
            $nbrSentences = 4;
            $average = 14;
            $stdDev = 3;

            // $int = rand(1262055681,1262055681);
            # When
            $result = $this->sut->getSentences($nbrSentences, $average, $stdDev);

            # Then
            $this->assertEquals($nbrSentences * $average, count(explode(" ", $result)));
        }
        // public function test_getMySQLTimeStamp() {
        //     # Given
        //     date_default_timezone_set("UTC");

        //     # When
        //     $result = $this->sut->getMySQLTimeStamp($from, $to);
        //     // echo $result;
        //     # Then
        //     // $expected = ;
        //     // $this->assertEquals($expected, $result);
        // }
	}
}