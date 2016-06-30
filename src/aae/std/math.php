<?php
/*
	unit tested
 */
namespace aae\std {
	/**
	 * @package aae\std
	 */
	class math extends \aae\abstr\FunctionCollection {
		public static function isOdd ($num) {
			return ( $num & 1 ) ? true : false;
		}
		public static function isEven ($num) {
			return !static::isOdd($num);
		}
	}
}