<?php
namespace aae\serialize {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class TsvTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Tsv();
		}
		
	}
}