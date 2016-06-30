<?php
/**
 *
 */
namespace aae\std {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class Color {
        protected $_hex_color, $_r, $_g, $_b;

        public function __construct($r = NULL, $g=-1, $b=-1) {
            $this->_r = $r;
            $this->_g = $g;
            $this->_b = $b;
            if (is_null($r)) {
                $hex_value = "#FFFFFF";
            } else $this->_hex_color = self::rgb2html($r, $g, $b);
        }
        public function __toString() {
            return $this->_hex_color;
        }
        private function _name_to_hex($name) {
            $hex_value = "#FFFFFF";
        }

        public static function rgb2html($r, $g=-1, $b=-1) {
            if (is_array($r) && sizeof($r) == 3)
                list($r, $g, $b) = $r;

            $r = intval($r); $g = intval($g);
            $b = intval($b);

            $r = dechex($r<0?0:($r>255?255:$r));
            $g = dechex($g<0?0:($g>255?255:$g));
            $b = dechex($b<0?0:($b>255?255:$b));

            $color = (strlen($r) < 2?'0':'').$r;
            $color .= (strlen($g) < 2?'0':'').$g;
            $color .= (strlen($b) < 2?'0':'').$b;
            return '#'.$color;
        }
        public function getRgb() {
            return [$this->_r, $this->_g, $this->_b];
        }
    }
}