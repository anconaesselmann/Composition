<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
	class JsonEscaper implements Escapeable {
		public function escape($string) {
            return json_encode($string);
        }
        public function getArrayElementSeparator() {
            return ",";
        }
        public function getArrayElementOpen() {
            return "";
        }
        public function getArrayElementClose() {
            return "";
        }
        public function getArrayOpen() {
            return "[";
        }
        public function getArrayClose() {
            return "]";
        }
	}
}