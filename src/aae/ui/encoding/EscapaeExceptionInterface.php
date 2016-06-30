<?php
/**
 *
 */
namespace aae\ui\encoding {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui\encoding
	 */
    interface EscapaeExceptionInterface {
        public function __construct($string);
        public function getRaw();
    }
}