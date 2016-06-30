<?php
/**
 *
 */
namespace aae\ui {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\ui
	 */
    class HtmlEscaper implements Escapeable {
        public function escape($string) {
            if ($string instanceof \aae\ui\encoding\EscapaeExceptionInterface) {
                if ($string instanceof \aae\ui\encoding\IsHtml) return $string->getRaw();
                $string = $string->getRaw();
            }
            return htmlspecialchars($string);
        }
        public function getArrayElementSeparator() {
            return "\n";
        }
        public function getArrayElementOpen() {
            return "";
        }
        public function getArrayElementClose() {
            return "";
        }
        public function getArrayOpen() {
            return "\n";//return "<div>";
        }
        public function getArrayClose() {
            return "\n";//return "</div>";
        }
    }
}