<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class HeadersTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Headers();
		}
		
	}
}