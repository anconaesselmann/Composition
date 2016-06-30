<?php
/**
 *
 */
namespace aae\str {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\str
	 */
	class VarSubstituter {
        private $_outputEscaper = NULL;

        public function __construct($outputEscaper = NULL) {
            $this->_outputEscaper = $outputEscaper;
        }
		public function resolveVars($string, $replacements, $escape = false, $varIdentifier = NULL) {
            if (is_null($varIdentifier)) {
                $varIdentifier = "\\$";
            }
            $replacementString = '~\{'.$varIdentifier.'(.*?)\}~si';
            return $this->_replace($string, $replacements, $escape, $replacementString);
        }
        private function _replace($string, $replacements, $escape, $replacementString) {
            $that = $this;
            return preg_replace_callback(
                $replacementString,
                function($match) use ($string, $replacements, $escape, $that) {
                    $replacement = $that->getReplacement($match, $replacements, $escape);
                    return str_replace(
                        $match[0],
                        $replacement,
                        $match[0]
                    );
                },
                $string
            );
        }
        private function _escape($string, $escape) {
            return ($escape) ? $this->_outputEscaper->escape($string) : $string;;
        }
        public function getReplacement($match, $replacements, $escape) {
            if (isset($replacements[$match[1]])) {
                $replacement = $this->_escape($replacements[$match[1]],$escape);
            } else {
                $replacement = $match[0];
            }
            if (is_array($replacement) &&
                count($replacement) === 1 &&
                array_key_exists("var", $replacement) &&
                array_key_exists($replacement["var"], $replacements)
            ) {
                $replacement = $replacements[$replacement["var"]];
            }
            return $replacement;
        }
	}
}