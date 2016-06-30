<?php
/**
 *
 */
namespace aae\std {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class Image {
		private $_min, $_max;
		public $medium = null;
		public function __construct($medium, $height, $width) {
			$this->medium = $medium;
			$this->medium->setDimensions($width, $height);
			$this->_min = NULL;
			$this->_max = NULL;
		}
		public function drawToFit($object, $parameters = NULL) {
			$extremePoints = $this->_getExtremePoints($object);
			$max = $extremePoints[0];
			$min = $extremePoints[1];
			if (is_null($this->_max)) {
				$this->_max = $max;
			}
			if (is_null($this->_min)) {
				$this->_min = $min;
			}
			if ($min->x < $this->_min->x) {
				$this->_min->x = $min->x;
			}
			if ($min->y < $this->_min->y) {
				$this->_min->y = $min->y;
			}
			if ($max->x > $this->_max->x) {
				$this->_max->x = $max->x;
			}
			if ($max->y > $this->_max->y) {
				$this->_max->y = $max->y;
			}
			$this->medium->setViewBox(
				floor($this->_min->y),
				floor($this->_min->x),
				$this->_max->y - $this->_min->y,
				$this->_max->x - $this->_min->x
			);
			$this->draw($object, $parameters);
		}
		public function _getExtremePoints($object) {
			$max = null;
			$min = null;
			foreach ($object as $item) {
				if (get_class($item) == "aae\math\cartesian\Point") {
					if (is_null($max)) {
						$max = clone $item;
						$min = clone $item;
					}
					$max = $this->_getMax($item, $max);
					$min = $this->_getMin($item, $min);
				} else {
					$extremePoints = $this->_getExtremePoints($item);
					if (is_null($max)) {
						$max = clone $extremePoints[0];
						$min = clone $extremePoints[1];
					}
					$max = $this->_getMax($extremePoints[0], $max);
					$min = $this->_getMin($extremePoints[1], $min);
				}
			}
			return array($max, $min);
		}
		protected function _getMax($point, $currentMax) {
			if ($point->x > $currentMax->x) {
				$currentMax->x = $point->x;
			}
			if ($point->y > $currentMax->y) {
				$currentMax->y = $point->y;
			}
			return $currentMax;
		}
		protected function _getMin($point, $currentMin) {
			if ($point->x < $currentMin->x) {
				$currentMin->x = $point->x;
			}
			if ($point->y < $currentMin->y) {
				$currentMin->y = $point->y;
			}
			return $currentMin;
		}
		public function draw($object, $parameters = NULL) {
			foreach ($object as $item) {
				switch (get_class($item)) {
					case 'aae\math\cartesian\Point':
						$this->medium->drawPoint($item, $parameters);
						break;
					case 'aae\math\cartesian\Line':
						$this->medium->drawLine($item, $parameters);
						break;
					case 'aae\math\cartesian\Sequence':
						$this->medium->drawSequence($item, $parameters);
						break;
					case 'aae\math\cartesian\Rect':
						$this->medium->drawRect($item, $parameters);
						break;
					case 'aae\math\cartesian\Pol':
						$this->medium->drawPol($item, $parameters);
						break;
					case 'aae\math\cartesian\Circle':
						$this->medium->drawCircle($item, $parameters);
						break;
					case 'aae\math\cartesian\Elypse':
						$this->medium->drawElypse($item, $parameters);
						break;
					case 'aae\math\cartesian\Curve':
						$this->medium->drawCurve($item, $parameters);
						break;

					default:
						$this->draw($item, $parameters);
						break;
				}
			}
		}
		public function strokeColor($r=NULL, $g=0, $b=0) {
			$this->medium->strokeColor($r, $g, $b);
		}
		public function __toString() {
			return (string)$this->medium;
		}
	}
}