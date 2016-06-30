<?php
/**
 *
 */
namespace aae\draw {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\draw
	 */
    class Color extends \aae\std\Color {
        public function __construct($r = NULL, $g = NULL, $b = NULL) {
            if (is_null($r)) {
                $this->_hex_color = NULL;
            } else {
                parent::__construct($r,$g,$b);
            }
        }
    }
}