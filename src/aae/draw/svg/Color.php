<?php
/**
 *
 */
namespace aae\draw\svg {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std\draw\svg\parameters
	 */
    class Color extends \aae\draw\Color {
        public function __construct($r = NULL, $g = NULL, $b = NULL) {
            if (is_null($r)) {
                $this->_hex_color = "none";
            } else {
                parent::__construct($r,$g,$b);
            }
        }
    }
}