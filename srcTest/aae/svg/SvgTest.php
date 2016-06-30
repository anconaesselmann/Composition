<?php
namespace aae\svg {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class SvgTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Svg(10, 10);
		}

		public function test_drawLine() {
			# Given a cartesian line
			$line = new \aae\math\cartesian\Line(new \aae\math\cartesian\Point(1,2), new \aae\math\cartesian\Point(3,4));
			$obj = new Svg(10, 10);
		
			# When drawLine is called
			$obj->drawLine($line);
			$result = (string)$obj;
			
			# Then a line is added to the image
			$expected = "EXPECTED_TEST_RESULT";
			#echo $result;
			#$this->assertEquals($expected, $result);
		}

		/*public function test_drawSequence() {
			# Given a cartesian line
			$sequence = new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(1,2),
							new \aae\math\cartesian\Point(3,4),
							new \aae\math\cartesian\Point(4,5)
			);
			
			$obj = new Svg(10, 10);
		
			# When drawSequence is called
			$obj->drawSequence($sequence);
			$result = (string)$obj;
			
			# Then a sequence is added to the image
			$expected = "EXPECTED_TEST_RESULT";
			//echo $result;
			$this->assertEquals($expected, $result);
		}*/
		
	}
}