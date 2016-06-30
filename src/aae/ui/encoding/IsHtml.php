<?php
/**
 *
 */
namespace aae\ui\encoding {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui\encoding
	 */
	class IsHtml implements EscapaeExceptionInterface {
        public $raw;
		public function __construct($string) {
            $this->raw = $string;
        }
        public function getRaw() {
            return $this->raw;
        }
	}
}