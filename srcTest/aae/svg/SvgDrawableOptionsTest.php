<?php
namespace aae\svg {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class SvgDrawableOptionsTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new SvgDrawableOptions();
		}
		
	}
}