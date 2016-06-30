<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	interface Escapeable {
        public function escape($string);
        public function getArrayElementSeparator();
        public function getArrayElementOpen();
        public function getArrayElementClose();
        public function getArrayOpen();
        public function getArrayClose();
    }
}