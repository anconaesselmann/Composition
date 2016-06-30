<?php
namespace aae\std {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ImageTest extends \PHPUnit_Framework_TestCase {

		protected function _getTree() {
			$tree = new \aae\adt\Tree();
			$args = func_get_args();
			foreach ($args as $value) {
				$tree->append($value);
			}
			return $tree;
		}
		public function provider__getExtremePoints() {
			return array(
				array(
					new \aae\math\cartesian\Sequence(
						new \aae\math\cartesian\Point(13,-55),
						new \aae\math\cartesian\Point(-99,12),
						new \aae\math\cartesian\Point(55,99)
					), 
					array(
						new \aae\math\cartesian\Point(55,99),
						new \aae\math\cartesian\Point(-99,-55),
					)
				),
				array(
					$this->_getTree(
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(13,-55),
							new \aae\math\cartesian\Point(-99,12),
							new \aae\math\cartesian\Point(55,99)
						)
					), 
					array(
						new \aae\math\cartesian\Point(55,99),
						new \aae\math\cartesian\Point(-99,-55),
					)
				),
				array(
					$this->_getTree(
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(13,-55)
						),
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(-99,12),
							new \aae\math\cartesian\Point(55,99)
						)
					), 
					array(
						new \aae\math\cartesian\Point(55,99),
						new \aae\math\cartesian\Point(-99,-55),
					)
				),
				array(
					$this->_getTree(
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(-13,-55)
						),
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(-99,-12),
							new \aae\math\cartesian\Point(-55,-99)
						)
					), 
					array(
						new \aae\math\cartesian\Point(-13,-12),
						new \aae\math\cartesian\Point(-99,-99),
					)
				),
				array(
					$this->_getTree(
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(13,55)
						),
						new \aae\math\cartesian\Sequence(
							new \aae\math\cartesian\Point(99,12),
							new \aae\math\cartesian\Point(55,99)
						)
					), 
					array(
						new \aae\math\cartesian\Point(99,99),
						new \aae\math\cartesian\Point(13,12),
					)
				),

			);
		}
		
		/**
		 * @dataProvider provider__getExtremePoints
		 */
		public function test__getExtremePoints($object, $expected) {
			# Given 
			$obj = new Image($this->getMock("aae\svg\Svg", null, array(0,0)), 0, 0);
		
			# When _getExtremePoints is called
			$result = $obj->_getExtremePoints($object);
			
			#var_dump($result);
			# Then 
			$this->assertEquals($expected, $result);
		}
		
	}
}