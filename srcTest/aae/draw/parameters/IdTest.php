<?php
namespace aae\draw\parameters {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class IdTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Id();
		}

	}
}