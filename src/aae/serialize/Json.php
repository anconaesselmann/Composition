<?php
/**
 *
 */
namespace aae\serialize {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\serialize
	 */
	class Json implements \aae\serialize\SerializerInterface {
		public function serialize($instance) {
			if (is_object($instance) && !$instance instanceof \JsonSerializable) {
				throw new \Exception("The object passed to ".__METHOD__." does not implement the JsonSerializable interface.", 216141337);	
			}
			return json_encode($instance);
		}
		public function unserialize($string) {
			if (!is_string($string)) {
				throw new \Exception(sprintf("Arguments passed to unserialize have to bee strings. %s passed instead.", gettype($string)), 223141616);
			}
			$json = json_decode($string, true);
			if (!$json) {
				$string = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $string);
				$json = json_decode($string, true);
			}
			if (!$json) {
				throw new \Exception("Invalid JSON was found in '$string'.", 216141338);
			}
			return $json;
		}
	}
}