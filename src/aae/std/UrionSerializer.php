<?php
/**
 *
 */
namespace aae\std {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\std
	 */
	class UrionSerializer implements \aae\serialize\SerializerInterface {
		public function serialize($instance) {
			if (is_object($instance) && !$instance instanceof \JsonSerializable) {
				throw new \Exception("The object passed to ".__METHOD__." does not implement the JsonSerializable interface.", 216141337);	
			}

			$jsonString = json_encode($instance);

			$urionString = $jsonString;


			$urionString = str_replace("{", "(", $urionString);
			$urionString = str_replace("}", ")", $urionString);
			$urionString = str_replace("\"", "", $urionString);
			$urionString = str_replace("[", "", $urionString);
			$urionString = str_replace("]", "", $urionString);

			return $urionString;
		}
		public function unserialize($string) {
			$pattern = "/(^[^\(])/";
			$string = preg_replace($pattern, "[\"$1", $string);

			$pattern = "/([^\)])$/";
			$string = preg_replace($pattern, "$1\"]", $string);

			$pattern = "/([^,])([,:\)])/";
			$string = preg_replace($pattern, "$1\"$2", $string);

			$pattern = "/([,:\(])([^,])/";
			$string = preg_replace($pattern, "$1\"$2", $string);

			$string = str_replace("(", "{", $string);
			$string = str_replace(")", "}", $string);
			$string = str_replace("'", "\"", $string);

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