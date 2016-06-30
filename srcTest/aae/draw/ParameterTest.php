<?php
namespace aae\draw {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class ParameterTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Parameter();
		}

	}
}