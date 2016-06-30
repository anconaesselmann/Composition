<?php
/**
 *
 */
namespace aae\geo\projection {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\math\projection
	 */
	class Mercator {
		const R = 6371000;
	
		public function map($object, $mapForScreen = false) {
			if (get_class($object) == 'aae\math\geospatial\Point') {
				return $this->mapPoint($object, $mapForScreen);
			} else if (get_class($object) == 'aae\math\geospatial\Sequence') {
				return $this->mapSequence($object, $mapForScreen);
			} else if (get_class($object) == 'aae\adt\Tree') {
				return $this->mapTree($object, $mapForScreen);
			} else {
				throw new \Exception("Unsupported type: " . get_class($object), 704141730);
			}
		}

		public function mapTree(\aae\adt\Tree $tree, $mapForScreen = false) {
			$cartTree = new \aae\adt\Tree();
			foreach ($tree as $element) {
				$item = $this->map($element, $mapForScreen);
				$cartTree->append($item);
			}
			return $cartTree;
		}

		public function mapSequence(\aae\math\geospatial\Sequence $sequence, $mapForScreen = false) {
			$cartSequence = new \aae\math\cartesian\Sequence();
			foreach ($sequence as $point) {
				$cartPoint = $this->mapPoint($point, $mapForScreen);
				$cartSequence->addElement($cartPoint);
			}
			return $cartSequence;
		}

		public function mapPoint(\aae\math\geospatial\Point $point, $mapForScreen = false) {
			#echo "lat: " . $point->lat . ", lon: " . $point->lon . "\n";
			$mfs = ($mapForScreen) ? -1 : 1;
			#$x = $this::R * $this->rad($point->lat);
			#$y = $this::R * log(tan(pi()/4 + $this->rad($point->lon)/2));

			$x = $this::R * $this->rad($point->lon);
			$y = $this::R * log(tan(pi()/4 + $this->rad($point->lat)/2));
			return new \aae\math\cartesian\Point($x, $mfs * $y);
		}

		public function rad($degree) {
			return $degree * pi() / 180;
		}
	}
}